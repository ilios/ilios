<?php

namespace App\Controller;

use App\Classes\UserEvent;
use App\Entity\Manager\IlmSessionManager;
use App\Entity\Manager\OfferingManager;
use App\Entity\Manager\UserManager;
use App\Entity\SessionInterface;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use \Eluceo\iCal\Component as ICS;
use Symfony\Component\Routing\RouterInterface;

class IcsController extends AbstractController
{
    const LOOK_BACK = '-4 months';
    const LOOK_FORWARD = '+2 months';

    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var UserManager
     */
    private $userManager;
    /**
     * @var OfferingManager
     */
    private $offeringManager;
    /**
     * @var IlmSessionManager
     */
    private $ilmSessionManager;

    /**
     * IcsController constructor.
     * @param RouterInterface $router
     * @param UserManager $userManager
     * @param OfferingManager $offeringManager
     * @param IlmSessionManager $ilmSessionManager
     */
    public function __construct(
        RouterInterface $router,
        UserManager $userManager,
        OfferingManager $offeringManager,
        IlmSessionManager $ilmSessionManager
    ) {
        $this->router = $router;
        $this->userManager = $userManager;
        $this->offeringManager = $offeringManager;
        $this->ilmSessionManager = $ilmSessionManager;
    }

    public function indexAction(Request $request, $key)
    {
        /** @var User $user */
        $user = $this->userManager->findOneBy(array('icsFeedKey' => $key));

        if (!$user) {
            throw new NotFoundHttpException();
        }

        $calendar = new ICS\Calendar('Ilios Calendar for ' . $user->getFirstAndLastName());
        $calendar->setPublishedTTL('P1H');

        $from = new \DateTime(self::LOOK_BACK);
        $to =  new \DateTime(self::LOOK_FORWARD);

        $events = $this->userManager->findEventsForUser($user->getId(), $from, $to);

        $publishedEvents = array_filter($events, function (UserEvent $event) {
            return $event->isPublished && !$event->isScheduled;
        });

        $scheduledEvents = array_filter($events, function (UserEvent $event) {
            return $event->isPublished && $event->isScheduled;
        });

        /* @var UserEvent $event */
        foreach ($publishedEvents as $event) {
            $vEvent = new ICS\Event();
            $vEvent->setDtStart($event->startDate);
            $vEvent->setDtEnd($event->endDate);
            $vEvent->setSummary($event->name);
            $vEvent->setLocation($event->location);
            $vEvent->setDescription($this->getDescriptionForEvent($event));
            $calendar->addComponent($vEvent);
        }

        foreach ($scheduledEvents as $event) {
            $vEvent = new ICS\Event();
            $vEvent
                ->setDtStart($event->startDate)
                ->setDtEnd($event->endDate)
                ->setSummary('Scheduled')
            ;
            $calendar->addComponent($vEvent);
        }

        $response = new Response();
        $response->setContent($calendar->render());
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
            $offering = $this->offeringManager->findOneBy(['id' => $event->offering]);
            /* @var SessionInterface $session */
            $session = $offering->getSession();
            $slug .= 'O' . $event->offering;
        }
        if ($event->ilmSession) {
            $ilmSession = $this->ilmSessionManager->findOneBy(['id' => $event->ilmSession]);
            $session = $ilmSession->getSession();
            $slug .= 'I' . $event->ilmSession;
        }
        $link = $this->router->generate(
            'ilios_web_assets',
            ['fileName' => "events/$slug"],
            UrlGenerator::ABSOLUTE_URL
        );

        $type = 'This offering is a(n) ' . $session->getSessionType()->getTitle();
        if ($session->isSupplemental()) {
            $type .= ' and is considered supplemental';
        }

        $lines = [
            $this->purify($type),
            $session->isAttireRequired()?'You will need special attire':null,
            $session->isEquipmentRequired()?'You will need special equipment':null,
            $session->isAttendanceRequired()?'Attendance is Required':null,
            "\n" . $link,
        ];

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
