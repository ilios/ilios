<?php

namespace Tests\WebBundle\DependencyInjection;

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

    public function testDefaultParametersSet()
    {
        $parameters = array(
            'ilios_web.frontend_release_version' => '',
            'ilios_web.keep_frontend_updated' => true,
        );
        $this->load();
        foreach ($parameters as $name => $value) {
            $this->assertContainerBuilderHasParameter($name, $value);
        }
    }

    public function testCustomParametersSet()
    {
        $faker = \Faker\Factory::create();
        $version = $faker->sha256;
        $this->load(array(
            'frontend_release_version' => $version,
            'keep_frontend_updated' => false,
        ));
        $this->assertContainerBuilderHasParameter(
            'ilios_web.frontend_release_version',
            $version
        );
        $this->assertContainerBuilderHasParameter(
            'ilios_web.keep_frontend_updated',
            false
        );
    }

    public function testServicesSet()
    {
        $services = array(
            'iliosweb.jsonindex',
        );
        $this->load();
        foreach ($services as $service) {
            $this->assertContainerBuilderHasService($service);
        }
    }
}
