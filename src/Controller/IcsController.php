<?php

declare(strict_types=1);

namespace App\Controller;

use App\Classes\UserEvent;
use App\Entity\SessionInterface;
use App\Entity\User;
use App\Repository\IlmSessionRepository;
use App\Repository\OfferingRepository;
use App\Repository\UserRepository;
use DateTime;
use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Domain\Entity\Event;
use Eluceo\iCal\Domain\ValueObject\DateTime as CalendarDateTime;
use Eluceo\iCal\Domain\ValueObject\Location;
use Eluceo\iCal\Domain\ValueObject\TimeSpan;
use Eluceo\iCal\Presentation\Factory\CalendarFactory;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RouterInterface;

class IcsController extends AbstractController
{
    private const LOOK_BACK = '-4 months';
    private const LOOK_FORWARD = '+2 months';

    private RouterInterface $router;
    private UserRepository $userRepository;
    private OfferingRepository $offeringRepository;
    private IlmSessionRepository $ilmSessionRepository;

    /**
     * IcsController constructor.
     * @param RouterInterface $router
     * @param UserRepository $userRepository
     * @param OfferingRepository $offeringRepository
     * @param IlmSessionRepository $ilmSessionRepository
     */
    public function __construct(
        RouterInterface $router,
        UserRepository $userRepository,
        OfferingRepository $offeringRepository,
        IlmSessionRepository $ilmSessionRepository
    ) {
        $this->router = $router;
        $this->userRepository = $userRepository;
        $this->offeringRepository = $offeringRepository;
        $this->ilmSessionRepository = $ilmSessionRepository;
    }

    public function indexAction(Request $request, $key)
    {
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['icsFeedKey' => $key]);

        if (!$user) {
            throw new NotFoundHttpException();
        }

        $vCalendar = new Calendar();
        $vCalendar->setProductIdentifier('Ilios Calendar for ' . $user->getFirstAndLastName());
        $from = new DateTime(self::LOOK_BACK);
        $to =  new DateTime(self::LOOK_FORWARD);

        $events = $this->userRepository->findEventsForUser($user->getId(), $from, $to);

        //add pre and post requisites so we can filter any prerequisites back out.
        $events = $this->userRepository->addPreAndPostRequisites($user->getId(), $events);
        $filteredEvents = array_filter($events, function (UserEvent $event) {
            return count($event->postrequisites) === 0;
        });
        $eventsWithPrePostRemoved = array_map(function (UserEvent $event) {
            $event->postrequisites = [];
            $event->prerequisites = [];
            return $event;
        }, $filteredEvents);

        $publishedEvents = array_filter($eventsWithPrePostRemoved, function (UserEvent $event) {
            return $event->isPublished && !$event->isScheduled;
        });

        $scheduledEvents = array_filter($eventsWithPrePostRemoved, function (UserEvent $event) {
            return $event->isPublished && $event->isScheduled;
        });

        /* @var UserEvent $event */
        foreach ($publishedEvents as $event) {
            $vEvent = new Event();
            $vEvent->setOccurrence(
                new TimeSpan(
                    new CalendarDateTime($event->startDate, false),
                    new CalendarDateTime($event->endDate, false)
                )
            );
            $vEvent->setSummary($event->name);
            if ($event->location) {
                $vEvent->setLocation(new Location($event->location));
            }
            $vEvent->setDescription($this->getDescriptionForEvent($event));
            $vCalendar->addEvent($vEvent);
        }

        foreach ($scheduledEvents as $event) {
            $vEvent = new Event();
            $vEvent->setOccurrence(
                new TimeSpan(
                    new CalendarDateTime($event->startDate, false),
                    new CalendarDateTime($event->endDate, false)
                )
            );
            $vEvent->setSummary('Scheduled');
            $vCalendar->addEvent($vEvent);
        }


        $componentFactory = new CalendarFactory();
        $calendarComponent = $componentFactory->createCalendar($vCalendar);
        $response = new Response();
        $response->setContent((string) $calendarComponent);
        $response->setCharset('utf-8');
        $response->headers->set('Content-Type', 'text/calendar');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $key . '.ics"');
        $response->prepare($request);
        return $response;
    }

    /**
     * @param UserEvent $event
     *
     * @return string
     */
    protected function getDescriptionForEvent(UserEvent $event)
    {
        $slug = 'U' . $event->startDate->format('Ymd');

        if ($event->offering) {
            $offering = $this->offeringRepository->findOneBy(['id' => $event->offering]);
            /* @var SessionInterface $session */
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
            'ilios_index',
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

    /**
     * @param $string
     * @return string
     */
    protected function purify($string)
    {
        return str_replace("\n", ' ', trim(strip_tags($string)));
    }
}
