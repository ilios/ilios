<?php
namespace Tests\WebBundle\DependencyInjection;

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
            'frontend_release_version' => $faker->sha256,
            'keep_frontend_updated' => true,
        );
        $this->assertProcessedConfigurationEquals(
            array($values),
            $values
        );
    }

    public function testDefaultValues()
    {
        $defaults = array(
            'keep_frontend_updated' => true,
            'frontend_release_version' => '',
        );
        $this->assertProcessedConfigurationEquals(
            array(),
            $defaults
        );
    }
}
