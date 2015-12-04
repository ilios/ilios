<?php
namespace Ilios\WebBundle\Tests\DependencyInjection;

use Ilios\WebBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\AbstractConfigurationTestCase;

class ConfigurationTest extends AbstractConfigurationTestCase
{
    protected function getConfiguration()
    {
        return new Configuration();
    }

    public function testSettingValues()
    {
        $faker = \Faker\Factory::create();
        $values = array(
            'environment' => 'staging',
            'version' => $faker->sha256,
            'production_bucket_path' => $faker->url,
            'staging_bucket_path' => $faker->url,
        );
        $this->assertProcessedConfigurationEquals(
            array($values),
            $values
        );
    }

    public function testDefaultValues()
    {
        $defaults = array(
            'environment' => 'production',
            'version' => false,
            'production_bucket_path' => 'https://s3-us-west-2.amazonaws.com/frontend-apiv1.0-index-prod/',
            'staging_bucket_path' => 'https://s3-us-west-2.amazonaws.com/frontend-apiv1.0-index-stage/',
        );
        $this->assertProcessedConfigurationEquals(
            array(),
            $defaults
        );
    }

    public function testBadEnvironemnt()
    {
        $this->assertConfigurationIsInvalid(
            array(
                array('environment' => 'bad')
            ),
            'environment'
        );
    }
}
