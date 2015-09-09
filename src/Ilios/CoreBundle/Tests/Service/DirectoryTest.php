<?php
namespace Ilios\CoreBundle\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Mockery as m;

use Ilios\CoreBundle\Service\Directory;

class DirectoryTest extends TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testConstructor()
    {
        $ldapManager = m::mock('Ilios\CoreBundle\Service\LdapManager');
        $obj = new Directory($ldapManager, 'campusId');
        $this->assertTrue($obj instanceof Directory);
    }

    public function testFindByCampusId()
    {
        $ldapManager = m::mock('Ilios\CoreBundle\Service\LdapManager');
        $obj = new Directory($ldapManager, 'campusId');
        $ldapManager->shouldReceive('search')->with('(campusId=1234)')->andReturn(array(1));
        
        $result = $obj->findByCampusId(1234);
        $this->assertSame($result, 1);
    }

    public function testFind()
    {
        $ldapManager = m::mock('Ilios\CoreBundle\Service\LdapManager');
        $obj = new Directory($ldapManager, 'campusId');
        $filter= '(&(|(sn=a*)(givenname=a*)(mail=a*))(|(sn=b*)(givenname=b*)(mail=b*)))';
        $ldapManager->shouldReceive('search')->with($filter, 'sn')->andReturn(array(1,2));
        
        $result = $obj->find(array('a', 'b'));
        $this->assertSame($result, array(1,2));
    }
}
