<?php

namespace Tests\WebBundle\DependencyInjection;

use Ilios\WebBundle\Service\WebIndexFromJson;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Ilios\WebBundle\DependencyInjection\IliosWebExtension;

class IliosWebExtensionTest extends AbstractExtensionTestCase
{

    protected function getContainerExtensions()
    {
        return array(
            new IliosWebExtension()
        );
    }

    public function testServicesSet()
    {
        $services = array(
            WebIndexFromJson::class,
        );
        $this->load();
        foreach ($services as $service) {
            $this->assertContainerBuilderHasService($service);
        }
    }
}
