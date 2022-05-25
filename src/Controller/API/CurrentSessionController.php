<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Classes\CurrentSession;
use App\Classes\SessionUserInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class CurrentSessionController
 * Current session reflects back the user from the token
 */
#[OA\Tag(name:'Current sessions')]
class CurrentSessionController extends AbstractController
{
    /**
     * Gets the currently authenticated users Id
     */
    #[Route(
        '/api/{version<v3>}/currentsession',
        methods: ['GET'],
    )]
    #[OA\Get(
        path: '/api/{version}/currentsession',
        summary: "Gets the user ID from the current session.",
        parameters: [
            new OA\Parameter(name: 'version', description: 'API Version', in: 'path'),
        ]
    )]
    #[OA\Response(
        response: '200',
        description: 'The current user id.',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    'userId',
                    type: 'string',
                )
            ],
            type: 'object'
        )
    )]
    #[OA\Response(response: '404', description: 'Not found.')]
    public function getCurrentSession(
        string $version,
        TokenStorageInterface $tokenStorage,
        SerializerInterface $serializer
    ): Response {
        $sessionUser = $tokenStorage->getToken()->getUser();
        if (!$sessionUser instanceof SessionUserInterface) {
            throw new NotFoundHttpException('No current session');
        }
        $currentSession = new CurrentSession($sessionUser);

        return new Response(
            $serializer->serialize($currentSession, 'json'),
            Response::HTTP_OK,
            ['Content-type' => 'application/json']
        );
    }
}
