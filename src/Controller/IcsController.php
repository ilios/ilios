<?php

declare(strict_types=1);

namespace App\Controller;

use App\Classes\UserEvent;
use App\Entity\SessionInterface;
use App\Entity\User;
use App\Repository\IlmSessionRepository;
use App\Repository\OfferingRepository;
use App\Repository\UserRepository;
use DateInterval;
use DateTime;
use Eluceo\iCal\Domain\ValueObject\Location;
use Exception;
use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Domain\Entity\Event;
use Eluceo\iCal\Domain\ValueObject\DateTime as IcalDateTime;
use Eluceo\iCal\Domain\ValueObject\TimeSpan;
use Eluceo\iCal\Presentation\Factory\CalendarFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RouterInterface;

class IcsController extends AbstractController
{
    private const string LOOK_BACK = '-4 months';
    private const string LOOK_FORWARD = '+2 months';

    public function __construct(
        private RouterInterface $router,
        private UserRepository $userRepository,
        private OfferingRepository $offeringRepository,
        private IlmSessionRepository $ilmSessionRepository
    ) {
    }

    #[Route(
        '/ics/{key}',
        requirements: [
            'key' => '^[a-zA-Z0-9]{64}$',
        ],
        methods: ['GET'],
    )]
    public function getICSFeed(Request $request, string $key): Response
    {
        $user = $this->userRepository->findOneBy(['icsFeedKey' => $key]);
        if (!$user) {
            throw new NotFoundHttpException();
        }

        $calendar = new Calendar();
        $calendar->setProductIdentifier('Ilios Calendar for ' . $user->getFirstAndLastName());
        $calendar->setPublishedTTL(new DateInterval('PT1H'));

        $from = new DateTime(self::LOOK_BACK);
        $to =  new DateTime(self::LOOK_FORWARD);

        $events = $this->userRepository->findEventsForUser($user->getId(), $from, $to);

        //add pre and post requisites so we can filter any prerequisites back out.
        $events = $this->userRepository->addPreAndPostRequisites($user->getId(), $events);
        $filteredEvents = array_filter($events, fn(UserEvent $event) => count($event->postrequisites) === 0);
        $eventsWithPrePostRemoved = array_map(function (UserEvent $event) {
            $event->postrequisites = [];
            $event->prerequisites = [];
            return $event;
        }, $filteredEvents);

        $publishedEvents = array_filter(
            $eventsWithPrePostRemoved,
            fn(UserEvent $event) => $event->isPublished && !$event->isScheduled
        );

        $scheduledEvents = array_filter(
            $eventsWithPrePostRemoved,
            fn(UserEvent $event) => $event->isPublished && $event->isScheduled
        );

        /** @var UserEvent $event */
        foreach ($publishedEvents as $event) {
            $vEvent = new Event();
            $vEvent->setOccurrence(
                new TimeSpan(new IcalDateTime($event->startDate, true), new IcalDateTime($event->endDate, true))
            );
            $vEvent->setSummary($event->name);
            if ($event->location) {
                $vEvent->setLocation(new Location($event->location));
            }
            $vEvent->setDescription($this->getDescriptionForEvent($event));
            $calendar->addEvent($vEvent);
        }

        foreach ($scheduledEvents as $event) {
            $vEvent = new Event();
            $vEvent->setOccurrence(
                new TimeSpan(new IcalDateTime($event->startDate, true), new IcalDateTime($event->endDate, true))
            );
            $vEvent->setSummary('Scheduled');
            $calendar->addEvent($vEvent);
        }

        $componentFactory = new CalendarFactory();
        $calendarComponent = $componentFactory->createCalendar($calendar);

        $response = new Response();
        $response->setContent((string) $calendarComponent);
        $response->setCharset('utf-8');
        $response->headers->set('Content-Type', 'text/calendar');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $key . '.ics"');
        $response->prepare($request);
        return $response;
    }

    protected function getDescriptionForEvent(UserEvent $event): string
    {
        $slug = 'U' . $event->startDate->format('Ymd');

        if ($event->offering) {
            $offering = $this->offeringRepository->findOneBy(['id' => $event->offering]);
            /** @var SessionInterface $session */
            $session = $offering->getSession();
            $slug .= 'O' . $event->offering;
        } elseif ($event->ilmSession) {
            $ilmSession = $this->ilmSessionRepository->findOneBy(['id' => $event->ilmSession]);
            $session = $ilmSession->getSession();
            $slug .= 'I' . $event->ilmSession;
        } else {
            throw new Exception("Event was neither an offering nor an ILM. This isn't a valid state");
        }
        $link = $this->router->generate(
            'app_index_index',
            ['fileName' => "events/$slug"],
            UrlGenerator::ABSOLUTE_URL
        );

        $type = 'This offering is a(n) ' . $session->getSessionType()->getTitle();
        if ($session->isSupplemental()) {
            $type .= ' and is considered supplemental';
        }

        $lines = [
            $this->purify($type),
            $session->isAttireRequired() ? 'You will need special attire' : null,
            $session->isEquipmentRequired() ? 'You will need special equipment' : null,
            $session->isAttendanceRequired() ? 'Attendance is Required' : null,
        ];

        if ($session->getPrerequisites()->count()) {
            $lines[] = 'Session has Pre-work';
        }
        if ($session->getLearningMaterials()->count() || $session->getCourse()->getLearningMaterials()->count()) {
            $lines[] = 'Session has Learning Materials';
        }

        $lines[] = "\n" . $link;

        //removes any empty values
        $lines = array_filter($lines);

        return implode("\n", $lines);
    }

    protected function purify(string $string): string
    {
        return str_replace("\n", ' ', trim(strip_tags($string)));
    }
}
