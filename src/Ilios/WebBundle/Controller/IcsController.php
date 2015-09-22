<?php

namespace Ilios\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use \Eluceo\iCal\Component as ICS;

class IcsController extends Controller
{
    public function indexAction($key)
    {
        $userManager = $this->container->get('ilioscore.user.manager');
        $user = $userManager->findUserBy(array('icsFeedKey' => $key));
        
        if (!$user) {
            throw new NotFoundHttpException();
        }
        
        $calendar = new ICS\Calendar('Ilios Calendar for ' . $user->getFirstAndLastName());
        $calendar->setPublishedTTL('P1H');
        
        $from = new \DateTime('-6 months');
        $to = new \DateTime('+6 months');

        $events = $userManager->findEventsForUser(
            $user->getId(),
            $from,
            $to
        );
        foreach ($events as $event) {
            $vEvent = new ICS\Event();
            $vEvent
                ->setDtStart($event->startDate)
                ->setDtEnd($event->endDate)
                ->setSummary($event->name)
                ->setCategories([$event->eventClass])
                ->setLocation($event->location)
            ;
            $calendar->addEvent($vEvent);
        }
        
        
        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $key . '.ics"');
        echo $calendar->render();

        exit();
    }
}
