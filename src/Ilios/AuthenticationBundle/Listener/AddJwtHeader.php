<?php

namespace Ilios\AuthenticationBundle\Listener;

use Ilios\AuthenticationBundle\Jwt\Token as JwtToken;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AddJwtHeader
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $token = $this->tokenStorage->getToken();
        if ($token instanceof JwtToken) {
            $response = $event->getResponse();
            $jwt = $token->getJwt();
            $response->headers->set('X-JWT-TOKEN', $jwt);
        }
    }
}
