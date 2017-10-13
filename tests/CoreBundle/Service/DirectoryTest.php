<?php
namespace Tests\CoreBundle\Service;

use Ilios\CoreBundle\Service\Config;
use Ilios\CoreBundle\Service\LdapManager;
use Mockery as m;

use Ilios\CoreBundle\Service\Directory;
use Tests\CoreBundle\TestCase;

class DirectoryTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    protected $ldapManager;
    protected $config;
    protected $obj;

    public function setup()
    {
        $this->ldapManager = m::mock(LdapManager::class);
        $this->config = m::mock(Config::class);
        $this->obj = new Directory(
            $this->ldapManager,
            $this->config
        );
    }

    public function tearDown()
    {
        unset($this->obj);
        unset($this->ldapManager);
        unset($this->config);
    }

    /**
     * @covers \Ilios\CoreBundle\Service\Directory::__construct
     */
    public function testConstructor()
    {
        $this->assertTrue($this->obj instanceof Directory);
    }

    /**
     * @covers \Ilios\CoreBundle\Service\Directory::findByCampusId
     */
    public function testFindByCampusId()
    {
        $this->config->shouldReceive('get')->once()->with('ldap_directory_campus_id_property')->andReturn('campusId');
        $this->ldapManager->shouldReceive('search')->with('(campusId=1234)')->andReturn(array(1));

        $result = $this->obj->findByCampusId(1234);
        $this->assertSame($result, 1);
    }

    /**
     * @covers \Ilios\CoreBundle\Service\Directory::findByCampusId
     */
    public function testFindByCampusIds()
    {
        $this->config->shouldReceive('get')->once()->with('ldap_directory_campus_id_property')->andReturn('campusId');
        $this->ldapManager->shouldReceive('search')->with('(|(campusId=1234)(campusId=1235))')->andReturn(array(1));

        $result = $this->obj->findByCampusIds([1234, 1235]);
        $this->assertSame($result, [1]);
    }

    /**
     * @covers \Ilios\CoreBundle\Service\Directory::findByCampusId
     */
    public function testFindByCampusIdsOnlyUseUnique()
    {
        $this->config->shouldReceive('get')->once()->with('ldap_directory_campus_id_property')->andReturn('campusId');
        $this->ldapManager->shouldReceive('search')
            ->with(m::mustBe('(|(campusId=1234)(campusId=1235))'))->andReturn(array(1));

        $result = $this->obj->findByCampusIds([1234, 1235, 1234, 1235]);
        $this->assertSame($result, [1]);
    }

    /**
     * @covers \Ilios\CoreBundle\Service\Directory::findByCampusId
     */
    public function testFindByCampusIdsInChunks()
    {
        $this->config->shouldReceive('get')->once()->with('ldap_directory_campus_id_property')->andReturn('campusId');
        $ids = [];
        $firstFilters = '(|';
        for ($i = 0; $i < 50; $i++) {
            $ids[] = $i;
            $firstFilters .= "(campusId=${i})";
        }
        $firstFilters .= ')';

        $secondFilters = '(|';
        for ($i = 50; $i < 100; $i++) {
            $ids[] = $i;
            $secondFilters .= "(campusId=${i})";
        }
        $secondFilters .= ')';

        $this->ldapManager->shouldReceive('search')
            ->with($firstFilters)->andReturn([1])->once();
        $this->ldapManager->shouldReceive('search')
            ->with($secondFilters)->andReturn([2])->once();

        $result = $this->obj->findByCampusIds($ids);
        $this->assertSame($result, [1, 2]);
    }

    /**
     * @covers \Ilios\CoreBundle\Service\Directory::find
     */
    public function testFind()
    {
        $this->config->shouldReceive('get')->once()->with('ldap_directory_campus_id_property')->andReturn('campusId');
        $filter = '(&(|(sn=a*)(givenname=a*)(mail=a*)(campusId=a*))(|(sn=b*)(givenname=b*)(mail=b*)(campusId=b*)))';
        $this->ldapManager->shouldReceive('search')->with($filter)->andReturn(array(1,2));

        $result = $this->obj->find(array('a', 'b'));
        $this->assertSame($result, array(1,2));
    }

    /**
     * @covers \Ilios\CoreBundle\Service\Directory::find
     */
    public function testFindOutputEscaping()
    {
        $this->config->shouldReceive('get')->once()->with('ldap_directory_campus_id_property')->andReturn('campusId');
        $filter = '(&(|(sn=a\2a*)(givenname=a\2a*)(mail=a\2a*)(campusId=a\2a*)))';
        $this->ldapManager->shouldReceive('search')->with($filter)->andReturn(array(1,2));

        $result = $this->obj->find(array('a*'));
        $this->assertSame($result, array(1,2));
    }

    /**
     * @covers \Ilios\CoreBundle\Service\Directory::findByLdapFilter
     */
    public function testFindByLdapFilter()
    {
        $filter = '(one)(two)';
        $this->ldapManager->shouldReceive('search')->with($filter)->andReturn(array(1,2));

        $result = $this->obj->findByLdapFilter($filter);
        $this->assertSame($result, array(1,2));
    }
}
