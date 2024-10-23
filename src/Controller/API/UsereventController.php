<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Classes\UserEvent;
use App\Classes\VoterPermissions;
use App\Entity\SessionInterface;
use App\Entity\UserInterface;
use App\Exception\InvalidInputWithSafeUserMessageException;
use App\Repository\SessionRepository;
use App\Repository\UserRepository;
use App\Traits\ApiAccessValidation;
use DateTime;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[OA\Tag(name:'User events')]
class UsereventController extends AbstractController
{
    use ApiAccessValidation;

    public function __construct(protected TokenStorageInterface $tokenStorage)
    {
    }

    #[Route(
        '/api/{version<v3>}/userevents/{id}',
        requirements: [
            'id' => '\d+',
        ],
        methods: ['GET'],
    )]
    #[OA\Get(
        path: "/api/{version}/userevents/{id}",
        summary: "Fetch all events for a given user.",
        parameters: [
            new OA\Parameter(name: 'version', description: 'API Version', in: 'path'),
            new OA\Parameter(name: 'id', description: 'User ID', in: 'path'),
            new OA\Parameter(
                name: 'from',
                description: 'Date of earliest event',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'date-time')
            ),
            new OA\Parameter(
                name: 'to',
                description: 'Date of latest event',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'date-time')
            ),
        ],
        responses: [
            new OA\Response(
                response: '200',
                description: 'An array of user events.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'userEvents',
                            type: 'array',
                            items: new OA\Items(
                                ref: new Model(type: UserEvent::class)
                            )
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: '403', description: 'Access Denied.'),
            new OA\Response(response: '404', description: 'Not Found.'),
        ]
    )]
    public function getEvents(
        string $version,
        int $id,
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker,
        UserRepository $repository,
        SessionRepository $sessionRepository,
        SerializerInterface $serializer,
    ): Response {
        $this->validateCurrentUserAsSessionUser();

        $user = $repository->findOneBy(['id' => $id]);
        if (!$user) {
            throw new NotFoundHttpException(sprintf('The user \'%s\' was not found.', $id));
        }

        if (!$authorizationChecker->isGranted(VoterPermissions::VIEW, $user)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        if ($sessionId = $request->get('session')) {
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
            fn($event) => $authorizationChecker->isGranted(VoterPermissions::VIEW, $event)
        ));

        $events = $repository->addPreAndPostRequisites($user->getId(), $events);

        // run pre-/post-requisite user events through the permissions checker
        for ($i = 0, $n = count($events); $i < $n; $i++) {
            /** @var UserEvent $event */
            $event = $events[$i];
            $event->prerequisites = array_values(
                array_filter(
                    $event->prerequisites,
                    fn($event) => $authorizationChecker->isGranted(VoterPermissions::VIEW, $event)
                )
            );
            $event->postrequisites = array_values(
                array_filter(
                    $event->postrequisites,
                    fn($event) => $authorizationChecker->isGranted(VoterPermissions::VIEW, $event)
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
            if (! $authorizationChecker->isGranted(VoterPermissions::VIEW_DRAFT_CONTENTS, $event)) {
                $event->clearDataForUnprivilegedUsers($now);
            }
        }

        $response['userEvents'] = $events ?: [];

        return new Response(
            $serializer->serialize($response, 'json'),
            Response::HTTP_OK,
            ['Content-type' => 'application/json']
        );
    }
}
