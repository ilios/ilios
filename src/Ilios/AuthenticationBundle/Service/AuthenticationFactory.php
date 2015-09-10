<?php

namespace Ilios\AuthenticationBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

class AuthenticationFactory
{
    /**
     * @var string
     */
    protected $authenticationServiceName;

    /**
     * @var ContainerInterface
     */
    protected $container;
    
    public function __construct(
        ContainerInterface $container,
        $authenticationServiceName
    ) {
        $this->authenticationServiceName = $authenticationServiceName;
        $this->container = $container;
    }
    
    public function createAuthenticationService()
    {
            return $this->container->get($this->authenticationServiceName);
    }
}
