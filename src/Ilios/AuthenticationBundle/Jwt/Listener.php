<?php
namespace Ilios\AuthenticationBundle\Jwt;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

use Ilios\AuthenticationBundle\Jwt\Token as JwtToken;

class Listener implements ListenerInterface
{
    protected $tokenStorage;
    protected $authenticationManager;
    protected $jwtKey;

    /**
     * @param TokenStorageInterface          $tokenStorage
     * @param AuthenticationManagerInterface $authenticationManager
     * @param string                         $secret                symfony provided secret key
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        AuthenticationManagerInterface $authenticationManager,
        $secret
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
        $this->jwtKey = 'ilios.jwt.key.' . $secret;
    }

    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        try {
            $token = new JwtToken($this->jwtKey);
            $token->setRequest($request);
            if ($token->isValidJwtRequest()) {
                $authToken = $this->authenticationManager->authenticate($token);
                $this->tokenStorage->setToken($authToken);
            }
        } catch (\UnexpectedValueException $e) {
            throw new BadCredentialsException('Invalid JSON Web Token: ' . $e->getMessage());

            return null;
        } catch (AuthenticationException $failed) {
            //We have a token with a bad user id, move on and let
            //another login method handle this
            return;
        }


        return;
    }
}
