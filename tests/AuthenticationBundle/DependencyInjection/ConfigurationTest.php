<?php
namespace Tests\AuthenticationBundle\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Ilios\AuthenticationBundle\DependencyInjection\Configuration;

/**
 * Class ConfigurationTest
 * @package Ilios\AuthenticationBundle\Tests\DependencyInjection
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @inheritdoc
     */
    protected function getConfiguration()
    {
        return new Configuration();
    }

    public function testRequiredConfigValues()
    {
        $this->assertConfigurationIsInvalid(
            array(
                array() // no values at all
            ),
            'type'
        );
    }
    
    public function testInvalidAuthType()
    {
        $this->assertConfigurationIsInvalid(
            array(
                array('type' => 'nothing') // no values at all
            ),
            'type'
        );
    }

    public function testInvalidCasCertificatePath()
    {
        $this->assertConfigurationIsInvalid(
            array(
                array('type' => 'form', 'cas_authentication_certificate_path' => '')
            ),
            'Unable to find certificate at'
        );
    }

    public function testValidCasCertificatePath()
    {
        $this->assertConfigurationIsValid(
            array(
                array('type' => 'form', 'cas_authentication_certificate_path' => __FILE__)
            )
        );
    }

    public function testInvalidCasVersion()
    {
        $this->assertConfigurationIsInvalid(
            array(
                array('type' => 'form', 'cas_authentication_version' => '99')
            ),
            'cas_authentication_version'
        );
    }
}
