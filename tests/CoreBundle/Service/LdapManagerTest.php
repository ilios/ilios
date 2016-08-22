<?php
namespace Tests\CoreBundle\Service;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Ilios\CoreBundle\Service\LdapManager;

class LdapManagerTest extends TestCase
{

    public function testConstructor()
    {
        $obj = new LdapManager('url', 'user', 'password', 'searchBase', 'campusId', 'username');
        $this->assertTrue($obj instanceof LdapManager);
    }
}
