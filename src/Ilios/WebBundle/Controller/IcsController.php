<?php

namespace Ilios\WebBundle\Controller;

use Ilios\CoreBundle\Classes\UserEvent;
use Ilios\CoreBundle\Entity\CourseLearningMaterialInterface;
use Ilios\CoreBundle\Entity\LearningMaterialInterface;
use Ilios\CoreBundle\Entity\LearningMaterialStatusInterface;
use Ilios\CoreBundle\Entity\ObjectiveInterface;
use Ilios\CoreBundle\Entity\SessionInterface;
use Ilios\CoreBundle\Entity\SessionLearningMaterialInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use \Eluceo\iCal\Component as ICS;

class IcsController extends Controller
{
    const LOOK_BACK = '-4 months';
    const LOOK_FORWARD = '+2 months';

    public function indexAction(Request $request, $key)
    {
        $manager = $this->container->get('ilioscore.user.manager');
        $user = $manager->findOneBy(array('icsFeedKey' => $key));

        if (!$user) {
            throw new NotFoundHttpException();
        }

        $calendar = new ICS\Calendar('Ilios Calendar for ' . $user->getFirstAndLastName());
        $calendar->setPublishedTTL('P1H');
        
        $from = new \DateTime(self::LOOK_BACK);
        $to =  new \DateTime(self::LOOK_FORWARD);

        $events = $manager->findEventsForUser($user->getId(), $from, $to);

        $publishedEvents = array_filter($events, function (UserEvent $event) {
            return $event->isPublished && !$event->isScheduled;
        });
        $publishedEvents = $manager->addInstructorsToEvents($publishedEvents);

        $scheduledEvents = array_filter($events, function (UserEvent $event) {
            return $event->isPublished && $event->isScheduled;
        });

        /* @var UserEvent $event */
        foreach ($publishedEvents as $event) {
            $vEvent = new ICS\Event();
            $vEvent
                ->setDtStart($event->startDate)
                ->setDtEnd($event->endDate)
                ->setSummary($event->name)
                ->setDescription($this->getDescriptionForEvent($event))
                ->setCategories([$event->eventClass])
                ->setLocation($event->location);
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
        if ($event->offering) {
            $offeringManager = $this->container->get('ilioscore.offering.manager');
            $offering = $offeringManager->findOneBy(['id' => $event->offering]);
            /* @var SessionInterface $session */
            $session = $offering->getSession();
        }
        if ($event->ilmSession) {
            $ilmSessionManager = $this->container->get('ilioscore.ilmsession.manager');
            $ilmSession = $ilmSessionManager->findOneBy(['id' => $event->ilmSession]);
            $session = $ilmSession->getSession();
        }

        $type = 'This offering is a(n) ' . $session->getSessionType()->getTitle();
        if ($session->isSupplemental()) {
            $type .= ' and is considered supplemental';
        }

        $description = null;
        if ($sessionDescription = $session->getSessionDescription()) {
            $description = $sessionDescription->getDescription();
        }

        $sessionObjectives = $session->getObjectives()->map(function (ObjectiveInterface $objective) {
            return $this->purify($objective->getTitle());
        })->toArray();

        $sessionMaterials =
            $session->getLearningMaterials()
                ->filter(function (SessionLearningMaterialInterface $learningMaterial) {
                    /** @var LearningMaterialInterface $lm */
                    $lm = $learningMaterial->getLearningMaterial();
                    $status = $lm->getStatus();
                    return $status->getId() !== LearningMaterialStatusInterface::IN_DRAFT;
                })
                ->toArray();

        $courseMaterials =
            $session->getCourse()->getLearningMaterials()
                ->filter(function (CourseLearningMaterialInterface $learningMaterial) {
                    /** @var LearningMaterialInterface $lm */
                    $lm = $learningMaterial->getLearningMaterial();
                    $status = $lm->getStatus();
                    return $status->getId() !== LearningMaterialStatusInterface::IN_DRAFT;
                })
                ->toArray();

        $callback = function ($lm1, $lm2) {
            $pos1 = $lm1->getPosition();
            $pos2 = $lm2->getPosition();
            if ($pos1 > $pos2) {
                return 1;
            } elseif ($pos1 < $pos2) {
                return -1;
            }

            $id1 = $lm1->getId();
            $id2 = $lm2->getId();

            if ($id1 > $id2) {
                return -1;
            } elseif ($id1 < $id2) {
                return 1;
            }
            return 0;
        };

        usort($sessionMaterials, $callback);
        usort($courseMaterials, $callback);

        $courseMaterials = array_map(function (CourseLearningMaterialInterface $learningMaterial) {
            return $this->getTextForLearningMaterial($learningMaterial->getLearningMaterial());
        }, $courseMaterials);

        $sessionMaterials = array_map(function (SessionLearningMaterialInterface $learningMaterial) {
            return $this->getTextForLearningMaterial($learningMaterial->getLearningMaterial());
        }, $sessionMaterials);

        $lines = [
            $this->purify($description),
            'Taught By ' . implode($event->instructors, '; '),
            $this->purify($type),
            $session->isAttireRequired()?'You will need special attire':null,
            $session->isEquipmentRequired()?'You will need special equiptment':null,
            "\nSession Objectives",
            implode($sessionObjectives, "\n"),
            "\nSession Learning Materials",
            implode("\n", $sessionMaterials),
            "\nCourse Learning Materials",
            implode("\n", $courseMaterials),
        ];

        //removes any empty values
        $lines = array_filter($lines);

        return implode("\n", $lines);
    }

    /**
     * @param LearningMaterialInterface $learningMaterial
     * @return string
     */
    protected function getTextForLearningMaterial(LearningMaterialInterface $learningMaterial)
    {
        $text = $this->purify($learningMaterial->getTitle()) . ' ';
        if ($citation = $learningMaterial->getCitation()) {
            $text .= $this->purify($citation);
        } elseif ($link = $learningMaterial->getLink()) {
            $text .= $this->purify($link);
        } else {
            $uri = $this->generateUrl('ilios_core_downloadlearningmaterial', array(
                'token' => $learningMaterial->getToken(),
            ), UrlGeneratorInterface::ABSOLUTE_URL);
            $text .= $uri;
        }

        return $text;
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
