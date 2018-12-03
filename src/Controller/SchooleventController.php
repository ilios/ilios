<?php

namespace App\Controller;

use App\Classes\CalendarEvent;
use App\Entity\Manager\SessionManager;
use App\Entity\SessionInterface;
use App\RelationshipVoter\AbstractVoter;
use App\Classes\SchoolEvent;
use App\Entity\Manager\SchoolManager;
use App\Exception\InvalidInputWithSafeUserMessageException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use DateTime;
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
    /**
     * @param string $version of the API requested
     * @param string $id of the school
     * @param Request $request
     * @param SchoolManager $schoolManager
     * @param SessionManager $sessionManager
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TokenStorageInterface $tokenStorage
     * @param SerializerInterface $serializer
     *
     * @return Response
     */
    public function getAction(
        $version,
        $id,
        Request $request,
        SchoolManager $schoolManager,
        SessionManager $sessionManager,
        AuthorizationCheckerInterface $authorizationChecker,
        TokenStorageInterface $tokenStorage,
        SerializerInterface $serializer
    ) {
        $school = $schoolManager->findOneBy(['id' => $id]);

        if (!$school) {
            throw new NotFoundHttpException(sprintf('The school \'%s\' was not found.', $id));
        }

        if ($sessionId = $request->get('session')) {
            /** @var SessionInterface $session */
            $session = $sessionManager->findOneBy(['id' => $sessionId]);

            if (!$session) {
                throw new NotFoundHttpException(sprintf('The session \'%s\' was not found.', $id));
            }
            $events = $schoolManager->findSessionEventsForSchool($school->getId(), $sessionId);
        } else {
            $fromTimestamp = $request->get('from');
            $toTimestamp = $request->get('to');
            $from = DateTime::createFromFormat('U', $fromTimestamp);
            $to = DateTime::createFromFormat('U', $toTimestamp);

            if (!$from) {
                throw new InvalidInputWithSafeUserMessageException("?from is missing or is not a valid timestamp");
            }
            if (!$to) {
                throw new InvalidInputWithSafeUserMessageException("?to is missing or is not a valid timestamp");
            }
            $events = $schoolManager->findEventsForSchool($school->getId(), $from, $to);
        }



        $events = array_filter($events, function ($entity) use ($authorizationChecker) {
            return $authorizationChecker->isGranted(AbstractVoter::VIEW, $entity);
        });

        $events = $schoolManager->addPreAndPostRequisites($events);
        $allEvents = [];
        /** @var CalendarEvent $event */
        foreach ($events as $event) {
            $allEvents[] = $event;
            $allEvents = array_merge($allEvents, $event->prerequisites);
            $allEvents = array_merge($allEvents, $event->postrequisites);
        }
        $allEvents = $schoolManager->addInstructorsToEvents($allEvents);
        $allEvents = $schoolManager->addMaterialsToEvents($allEvents);
        $allEvents = $schoolManager->addSessionDataToEvents($allEvents);

        $sessionUser = $tokenStorage->getToken()->getUser();

        //Un-privileged users get less data
        $hasElevatedPrivileges = $sessionUser->isRoot() || $sessionUser->performsNonLearnerFunction();
        if (! $hasElevatedPrivileges) {
            /** @var SchoolEvent $event */
            $now = new \DateTime();
            foreach ($allEvents as $event) {
                $event->clearDataForUnprivilegedUsers($now);
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
