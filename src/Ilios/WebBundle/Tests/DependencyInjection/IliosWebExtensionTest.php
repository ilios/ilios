<?php

namespace Ilios\WebBundle\Tests\DependencyInjection;

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
            'ilios_web.environment' => 'production',
            'ilios_web.version' => false,
            'ilios_web.bucket_url' => 'https://s3-us-west-2.amazonaws.com/frontend-apiv1.0-index-prod/',
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
        $bucketPath = $faker->url;
        $this->load(array(
            'environment' => 'staging',
            'version' => $version,
            'staging_bucket_path' => $bucketPath,
        ));
        $this->assertContainerBuilderHasParameter(
            'ilios_web.environment',
            'staging'
        );
        $this->assertContainerBuilderHasParameter(
            'ilios_web.version',
            $version
        );
        $this->assertContainerBuilderHasParameter(
            'ilios_web.bucket_url',
            $bucketPath
        );
    }

    public function testCustomProductionBucketSet()
    {
        $faker = \Faker\Factory::create();
        $values = array(
            'production_bucket_path' => $faker->url,
        );
        $this->load($values);
        $this->assertContainerBuilderHasParameter(
            'ilios_web.bucket_url',
            $values['production_bucket_path']
        );
    }

    public function testServicesSet()
    {
        $services = array(
            'iliosweb.assets',
        );
        $this->load();
        foreach ($services as $service) {
            $this->assertContainerBuilderHasService($service);
        }
    }
}
