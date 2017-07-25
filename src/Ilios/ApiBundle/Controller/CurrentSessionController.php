<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Classes\CurrentSession;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class CurrentSessionController
 * Current session reflects back the user from the token
 */
class CurrentSessionController extends AbstractController
{
    /**
     * Gets the currently authenticated users Id
     *
     * @param string $version
     *
     * @return Response
     */
    public function getAction($version, TokenStorageInterface $tokenStorage, SerializerInterface $serializer)
    {
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
