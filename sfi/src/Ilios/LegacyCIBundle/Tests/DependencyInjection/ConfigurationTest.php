<?php
namespace Ilios\LegacyCIBundle\Tests\DependencyInjection;

use Ilios\LegacyCIBundle\DependencyInjection\Configuration;
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
            'cookie_name' => $faker->text,
            'encryption_key' => $faker->sha256,
            'encrypt_cookie' => true
        );
        $this->assertProcessedConfigurationEquals(
            array(array('session' => $values)),
            array('session'=> $values)
        );
    }
    
    public function testDefaultValues()
    {
        $this->assertProcessedConfigurationEquals(
            array(array('session' => array('encryption_key' => 'foo'))),
            array(
                'session'=> array(
                    'cookie_name' => 'ci_session',
                    'encryption_key' => 'foo',
                    'encrypt_cookie' => false
                )
            )
        );
    }
    
    public function testMissingValues()
    {
        $this->assertConfigurationIsInvalid(
            array(
                array()
            ),
            'session'
        );
        
        $this->assertConfigurationIsInvalid(
            array(
                array('session' => array())
            ),
            'encryption_key'
        );
    }
}
