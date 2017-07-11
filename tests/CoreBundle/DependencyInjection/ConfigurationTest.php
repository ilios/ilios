<?php
namespace Tests\CoreBundle\DependencyInjection;

use Ilios\CoreBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;

class ConfigurationTest
{
    use ConfigurationTestCaseTrait;

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
            'file_system_storage_path'
        );
    }
}
