<?php

declare(strict_types=1);

namespace App\Controller;

use App\Classes\CalendarEvent;
use App\Classes\SessionUserInterface;
use App\Entity\SessionInterface;
use App\RelationshipVoter\AbstractCalendarEvent;
use App\RelationshipVoter\AbstractVoter;
use App\Classes\UserEvent;
use App\Entity\UserInterface;
use App\Exception\InvalidInputWithSafeUserMessageException;
use App\Repository\SessionRepository;
use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use DateTime;
use Symfony\Component\Routing\Annotation\Route;
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
     */
    #[Route(
        '/api/{version}/userevents/{id}',
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
        AuthorizationCheckerInterface $authorizationChecker,
        UserRepository $repository,
        SessionRepository $sessionRepository,
        SerializerInterface $serializer,
        TokenStorageInterface $tokenStorage
    ): Response {
        /** @var UserInterface $user */
        $user = $repository->findOneBy(['id' => $id]);

        if (!$user) {
            throw new NotFoundHttpException(sprintf('The user \'%s\' was not found.', $id));
        }

        if (!$authorizationChecker->isGranted(AbstractVoter::VIEW, $user)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        if ($sessionId = $request->get('session')) {
            /** @var SessionInterface $session */
            $session = $sessionRepository->findOneBy(['id' => $sessionId]);

            if (!$session) {
                throw new NotFoundHttpException(sprintf('The session \'%s\' was not found.', $id));
            }
            $events = $repository->findSessionEventsForUser($user->getId(), $session->getId());
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
            $events = $repository->findEventsForUser($user->getId(), $from, $to);
        }

        $events = array_values(array_filter(
            $events,
            fn($event) => $authorizationChecker->isGranted(AbstractVoter::VIEW, $event)
        ));

        $events = $repository->addPreAndPostRequisites($user->getId(), $events);

        // run pre-/post-requisite user events through the permissions checker
        for ($i = 0, $n = count($events); $i < $n; $i++) {
            /** @var UserEvent $event */
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
        /** @var UserEvent $event */
        foreach ($events as $event) {
            $allEvents[] = $event;
            $allEvents = array_merge($allEvents, $event->prerequisites);
            $allEvents = array_merge($allEvents, $event->postrequisites);
        }
        $allEvents = $repository->addInstructorsToEvents($allEvents);
        $allEvents = $repository->addMaterialsToEvents($allEvents);
        $allEvents = $repository->addSessionDataToEvents($allEvents);

        $now = new DateTime();
        foreach ($allEvents as $event) {
            if (! $authorizationChecker->isGranted(AbstractCalendarEvent::VIEW_DRAFT_CONTENTS, $event)) {
                $event->clearDataForUnprivilegedUsers($now);
            }
        }

        $response['userEvents'] = $events ? $events : [];

        return new Response(
            $serializer->serialize($response, 'json'),
            Response::HTTP_OK,
            ['Content-type' => 'application/json']
        );
    }
}
