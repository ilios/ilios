<?php

declare(strict_types=1);

namespace App\Controller;

use App\Classes\SessionUserInterface;
use App\Entity\SessionInterface;
use App\RelationshipVoter\AbstractCalendarEvent;
use App\RelationshipVoter\SchoolEvent as SchoolEventVoter;
use App\RelationshipVoter\AbstractVoter;
use App\Classes\SchoolEvent;
use App\Exception\InvalidInputWithSafeUserMessageException;
use App\Repository\SchoolRepository;
use App\Repository\SessionRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use DateTime;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class SchooleventController
 *
 * Search for events happening in a school
 */
class SchooleventController extends AbstractController
{
    #[Route(
        '/api/{version}/schoolevents/{id}',
        requirements: [
            'version' => '%ilios_api_valid_api_versions%',
            'id' => '\d+',
        ],
        methods: ['GET'],
    )]
    public function getEvents(
        string $version,
        int $id,
        Request $request,
        SchoolRepository $schoolRepository,
        SessionRepository $sessionRepository,
        AuthorizationCheckerInterface $authorizationChecker,
        TokenStorageInterface $tokenStorage,
        SerializerInterface $serializer
    ): Response {
        $school = $schoolRepository->findOneBy(['id' => $id]);

        if (!$school) {
            throw new NotFoundHttpException(sprintf('The school \'%s\' was not found.', $id));
        }

        if ($sessionId = $request->get('session')) {
            /** @var SessionInterface $session */
            $session = $sessionRepository->findOneBy(['id' => $sessionId]);

            if (!$session) {
                throw new NotFoundHttpException(sprintf('The session \'%s\' was not found.', $id));
            }
            $events = $schoolRepository->findSessionEventsForSchool($school->getId(), $session->getId());
        } else {
            $fromTimestamp = $request->get('from') ?? '';
            $toTimestamp = $request->get('to') ?? '';
            $from = DateTime::createFromFormat('U', $fromTimestamp);
            $to = DateTime::createFromFormat('U', $toTimestamp);

            if (!$from) {
                throw new InvalidInputWithSafeUserMessageException("?from is missing or is not a valid timestamp");
            }
            if (!$to) {
                throw new InvalidInputWithSafeUserMessageException("?to is missing or is not a valid timestamp");
            }
            $events = $schoolRepository->findEventsForSchool($school->getId(), $from, $to);
        }

        $events = array_values(array_filter(
            $events,
            fn($event) => $authorizationChecker->isGranted(AbstractVoter::VIEW, $event)
        ));

        /** @var SessionUserInterface $sessionUser */
        $sessionUser = $tokenStorage->getToken()->getUser();

        $events = $schoolRepository->addPreAndPostRequisites($id, $events);

        // run pre-/post-requisite user events through the permissions checker
        for ($i = 0, $n = count($events); $i < $n; $i++) {
            /** @var SchoolEvent $event */
            $event = $events[$i];
            $event->prerequisites = array_values(
                array_filter(
                    $event->prerequisites,
                    fn($event) => $authorizationChecker->isGranted(AbstractVoter::VIEW, $event)
                )
            );
            $event->postrequisites = array_values(
                array_filter(
                    $event->postrequisites,
                    fn($event) => $authorizationChecker->isGranted(AbstractVoter::VIEW, $event)
                )
            );
        }

        // flatten out nested events, so that we can attach additional data points, and blank out data, in one go.
        $allEvents = [];
        foreach ($events as $event) {
            $allEvents[] = $event;
            $allEvents = array_merge($allEvents, $event->prerequisites);
            $allEvents = array_merge($allEvents, $event->postrequisites);
        }
        $allEvents = $schoolRepository->addInstructorsToEvents($allEvents);
        $allEvents = $schoolRepository->addMaterialsToEvents($allEvents);
        $allEvents = $schoolRepository->addSessionDataToEvents($allEvents);

        $now = new DateTime();
        foreach ($allEvents as $event) {
            if (! $authorizationChecker->isGranted(AbstractCalendarEvent::VIEW_DRAFT_CONTENTS, $event)) {
                if (
                    $sessionUser->isStudentAdvisorInCourse($event->course) ||
                    $sessionUser->isStudentAdvisorInSession($event->session) ||
                    ($event->offering && $sessionUser->isLearnerInOffering($event->offering)) ||
                    ($event->ilmSession && $sessionUser->isLearnerInIlm($event->ilmSession))
                ) {
                    $event->clearDataForStudentAssociatedWithEvent($now);
                } else {
                    $event->clearDataForUnprivilegedUsers();
                }
            }

            if (! $authorizationChecker->isGranted(SchoolEventVoter::VIEW_VIRTUAL_LINK, $event)) {
                $event->url = null;
            }
        }

        $response['events'] = $events ? array_values($events) : [];
        return new Response(
            $serializer->serialize($response, 'json'),
            Response::HTTP_OK,
            ['Content-type' => 'application/json']
        );
    }
}
