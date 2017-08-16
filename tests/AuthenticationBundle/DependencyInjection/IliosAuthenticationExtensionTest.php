<?php

namespace Tests\AuthenticationBundle\DependencyInjection;

use Ilios\AuthenticationBundle\Listener\AddJwtHeader;
use Ilios\AuthenticationBundle\Service\AuthenticationFactory;
use Ilios\AuthenticationBundle\Service\CasManager;
use Ilios\AuthenticationBundle\Service\JsonWebTokenManager;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Ilios\AuthenticationBundle\DependencyInjection\IliosAuthenticationExtension;

class IliosAuthenticationExtensionTest extends AbstractExtensionTestCase
{

    protected function getContainerExtensions()
    {
        return array(
            new IliosAuthenticationExtension()
        );
    }

    public function testServicesSet()
    {
        $services = array(
            AddJwtHeader::class,
            JsonWebTokenManager::class,
            AuthenticationFactory::class,
            CasManager::class,
        );
        $this->load();
        foreach ($services as $service) {
            $this->assertContainerBuilderHasService($service);
        }
    }
}
