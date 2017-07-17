<?php
namespace Tests\CoreBundle\Service;

use Ilios\CoreBundle\Service\Config;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Ilios\CoreBundle\Service\LdapManager;
use Mockery as m;

class LdapManagerTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    public function testConstructor()
    {
        $config = m::mock(Config::class);
        $obj = new LdapManager($config);
        $this->assertTrue($obj instanceof LdapManager);
    }
}
