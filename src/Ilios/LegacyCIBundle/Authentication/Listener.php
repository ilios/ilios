<?php
namespace Ilios\LegacyCIBundle\Authentication;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Ilios\LegacyCIBundle\Authentication\Token;

class Listener implements ListenerInterface
{
    protected $securityContext;
    protected $authenticationManager;
    protected $ciToken;

    public function __construct(
        SecurityContextInterface $securityContext,
        AuthenticationManagerInterface $authenticationManager,
        Token $token
    ) {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->ciToken = $token;
    }

    public function handle(GetResponseEvent $event)
    {
        try {
            $authToken = $this->authenticationManager->authenticate($this->ciToken);
            $this->securityContext->setToken($authToken);

            return;
        } catch (AuthenticationException $failed) {
            // ... you might log something here
        }

        // By default deny authorization
        $response = new Response();
        $response->setStatusCode(Response::HTTP_FORBIDDEN);
        $event->setResponse($response);
    }
}
