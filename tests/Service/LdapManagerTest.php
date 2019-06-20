<?php
namespace App\Tests\Service;

use App\Service\Config;
use App\Service\LdapManager;
use App\Tests\TestCase;
use Mockery as m;

class LdapManagerTest extends TestCase
{
    public function testConstructor()
    {
        $config = m::mock(Config::class);
        $obj = new LdapManager($config);
        $this->assertTrue($obj instanceof LdapManager);
    }
}
