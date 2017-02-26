<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\CoreBundle\Classes\CurrentSession;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class CurrentSessionController
 * Current session reflects back the user from the token
 * @package Ilios\ApiBundle\Controller
 */
class CurrentSessionController extends Controller
{
    /**
     * Gets the currently authenticated users Id
     *
     * @param string $version
     *
     * @return Response
     */
    public function getAction($version)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (!$user instanceof UserInterface) {
            throw new NotFoundHttpException('No current session');
        }
        $currentSession = new CurrentSession($user);

        $serializer = $this->get('serializer');
        return new Response(
            $serializer->serialize($currentSession, 'json'),
            Response::HTTP_OK,
            ['Content-type' => 'application/json']
        );
    }
}
