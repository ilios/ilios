<?php

namespace Ilios\CoreBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Ilios\CoreBundle\DependencyInjection\IliosCoreExtension;

class IliosAuthenticationExtensionTest extends AbstractExtensionTestCase
{

    protected function getContainerExtensions()
    {
        return array(
            new IliosCoreExtension()
        );
    }

    public function testParametersSet()
    {
        $fileSystemStoragePath = '/tmp/test';
        $this->load(array(
            'file_system_storage_path' => $fileSystemStoragePath,
        ));
        $parameters = array(
            
            'ilios_core.file_store_path' => $fileSystemStoragePath,
        );
        foreach ($parameters as $name => $value) {
            $this->assertContainerBuilderHasParameter($name, $value);
        }
        $services = array(
            'ilioscore.filesystem',
        );
        foreach ($services as $service) {
            $this->assertContainerBuilderHasService($service);
        }
    }
}
