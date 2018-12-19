<?php

namespace App\Controller;

use App\Classes\CalendarEvent;
use App\Classes\SessionUserInterface;
use App\Entity\Manager\SessionManager;
use App\Entity\SessionInterface;
use App\RelationshipVoter\AbstractVoter;
use App\Classes\UserEvent;
use App\Entity\Manager\UserManager;
use App\Entity\UserInterface;
use App\Exception\InvalidInputWithSafeUserMessageException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use DateTime;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class UsereventController
 */
class UsereventController extends AbstractController
{
    /**
     * Get events for a user
     *
     * @param string $version
     * @param int $id of the user
     * @param Request $request
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param UserManager $manager
     * @param SessionManager $sessionManager
     * @param SerializerInterface $serializer
     * @param TokenStorageInterface $tokenStorage
     *
     * @return Response
     * @throws \Exception
     */
    public function getAction(
        $version,
        $id,
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker,
        UserManager $manager,
        SessionManager $sessionManager,
        SerializerInterface $serializer,
        TokenStorageInterface $tokenStorage
    ) {
        /** @var UserInterface $user */
        $user = $manager->findOneBy(['id' => $id]);

        if (!$user) {
            throw new NotFoundHttpException(sprintf('The user \'%s\' was not found.', $id));
        }

        if (!$authorizationChecker->isGranted(AbstractVoter::VIEW, $user)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        if ($sessionId = $request->get('session')) {
            /** @var SessionInterface $session */
            $session = $sessionManager->findOneBy(['id' => $sessionId]);

            if (!$session) {
                throw new NotFoundHttpException(sprintf('The session \'%s\' was not found.', $id));
            }
            $events = $manager->findSessionEventsForUser($user->getId(), $sessionId);
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
            $events = $manager->findEventsForUser($user->getId(), $from, $to);
        }

        $events = array_filter(
            $events,
            function ($event) use ($authorizationChecker) {
                return $authorizationChecker->isGranted(AbstractVoter::VIEW, $event);
            }
        );
        /** @var SessionUserInterface $sessionUser */
        $sessionUser = $tokenStorage->getToken()->getUser();

        $events = $manager->addPreAndPostRequisites($user->getId(), $events);

        // run pre-/post-requisite user events through the permissions checker
        for ($i = 0, $n = count($events); $i < $n; $i++) {
            /** @var UserEvent $event */
            $event = $events[$i];
            $event->prerequisites = array_values(
                array_filter(
                    $event->prerequisites,
                    function ($event) use ($authorizationChecker) {
                        return $authorizationChecker->isGranted(AbstractVoter::VIEW, $event);
                    }
                )
            );
            $event->postrequisites = array_values(
                array_filter(
                    $event->postrequisites,
                    function ($event) use ($authorizationChecker) {
                        return $authorizationChecker->isGranted(AbstractVoter::VIEW, $event);
                    }
                )
            );
        }

        // flatten out nested events, so that we can attach additional data points, and blank out data, in one go.
        $allEvents = [];
        /** @var UserEvent $event */
        foreach ($events as $event) {
            $allEvents[] = $event;
            $allEvents = array_merge($allEvents, $event->prerequisites);
            $allEvents = array_merge($allEvents, $event->postrequisites);
        }
        $allEvents = $manager->addInstructorsToEvents($allEvents);
        $allEvents = $manager->addMaterialsToEvents($allEvents);
        $allEvents = $manager->addSessionDataToEvents($allEvents);

        // Remove all draft data when not viewing your own events
        // or if the requesting user does not have elevated privileges
        $hasElevatedPrivileges = $sessionUser->isRoot() || $sessionUser->performsNonLearnerFunction();
        if ($sessionUser->getId() !== $user->getId() || !$hasElevatedPrivileges) {
            $now = new \DateTime();
            /* @var CalendarEvent $event */
            foreach ($allEvents as $event) {
                $event->clearDataForUnprivilegedUsers($now);
            }
        }

        $response['userEvents'] = $events ? array_values($events) : [];

        return new Response(
            $serializer->serialize($response, 'json'),
            Response::HTTP_OK,
            ['Content-type' => 'application/json']
        );
    }
}
