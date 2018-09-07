<?php

namespace Tests\AuthenticationBundle\DependencyInjection;

use AppBundle\Listener\AddJwtHeader;
use AppBundle\Service\AuthenticationFactory;
use AppBundle\Service\CasManager;
use AppBundle\Service\JsonWebTokenManager;
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
