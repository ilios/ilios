<?php

namespace Ilios\CoreBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Ilios\CoreBundle\DependencyInjection\IliosCoreExtension;

class IliosCoreExtensionTest extends AbstractExtensionTestCase
{

    protected function getContainerExtensions()
    {
        return array(
            new IliosCoreExtension()
        );
    }

    public function testParametersSet()
    {
        $this->load();
        $parameters = array(
            'ilios_core.objective_handler.class' => 'Ilios\CoreBundle\Handler\ObjectiveHandler',
            'ilios_core.objective_entity.class' => 'Ilios\CoreBundle\Entity\Objective',
            'ilios_core.listener.container_aware.class' => 'Ilios\CoreBundle\EventListener\ContainerInjector'
        );
        foreach ($parameters as $name => $value) {
            $this->assertContainerBuilderHasParameter($name, $value);
        }
        $services = array(
            'ilios_core.objective_handler',
            'ilios_core.listener.container_aware',
        );
        foreach ($services as $service) {
            $this->assertContainerBuilderHasService($service);
        }
    }
}
