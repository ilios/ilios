<?php
namespace Ilios\CoreBundle\Tests\DependencyInjection;

use Ilios\CoreBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\AbstractConfigurationTestCase;

class ConfigurationTest extends AbstractConfigurationTestCase
{
    protected function getConfiguration()
    {
        return new Configuration();
    }
    
    public function testEmptyConfiguration()
    {
        $this->assertProcessedConfigurationEquals(
            array(),
            array()
        );
    }
}
