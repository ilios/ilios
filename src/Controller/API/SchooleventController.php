<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Classes\SchoolEvent;
use App\Classes\SessionUserInterface;
use App\Classes\VoterPermissions;
use App\Entity\SessionInterface;
use App\Exception\InvalidInputWithSafeUserMessageException;
use App\Repository\SchoolRepository;
use App\Repository\SessionRepository;
use App\Traits\ApiAccessValidation;
use DateTime;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[OA\Tag(name:'School events')]
class SchooleventController extends AbstractController
{
    use ApiAccessValidation;

    public function __construct(protected TokenStorageInterface $tokenStorage)
    {
    }

    #[Route(
        '/api/{version<v3>}/schoolevents/{id}',
        requirements: [
            'id' => '\d+',
        ],
        methods: ['GET'],
    )]
    #[OA\Get(
        path: "/api/{version}/schoolevents/{id}",
        summary: "Fetch all events for a given school.",
        parameters: [
            new OA\Parameter(name: 'version', description: 'API Version', in: 'path'),
            new OA\Parameter(name: 'id', description: 'School ID', in: 'path'),
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
                description: 'An array of school events.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'events',
                            type: 'array',
                            items: new OA\Items(
                                ref: new Model(type: SchoolEvent::class)
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
        SchoolRepository $schoolRepository,
        SessionRepository $sessionRepository,
        AuthorizationCheckerInterface $authorizationChecker,
        TokenStorageInterface $tokenStorage,
        SerializerInterface $serializer
    ): Response {
        $this->validateCurrentUserAsSessionUser();

        /** @var SessionUserInterface $sessionUser */
        $sessionUser = $tokenStorage->getToken()->getUser();

        $school = $schoolRepository->findOneBy(['id' => $id]);

        if (!$school) {
            throw new NotFoundHttpException(sprintf('The school \'%s\' was not found.', $id));
        }

        if ($sessionId = $request->get('session')) {
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
            fn($event) => $authorizationChecker->isGranted(VoterPermissions::VIEW, $event)
        ));

        $events = $schoolRepository->addPreAndPostRequisites($id, $events);

        // run pre-/post-requisite school events through the permissions checker
        for ($i = 0, $n = count($events); $i < $n; $i++) {
            /** @var SchoolEvent $event */
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
            if (! $authorizationChecker->isGranted(VoterPermissions::VIEW_DRAFT_CONTENTS, $event)) {
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

            if (! $authorizationChecker->isGranted(VoterPermissions::VIEW_VIRTUAL_LINK, $event)) {
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
