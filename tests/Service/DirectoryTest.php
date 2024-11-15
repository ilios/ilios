<?php

declare(strict_types=1);

namespace App\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use App\Service\Config;
use App\Service\LdapManager;
use Mockery as m;
use App\Service\Directory;
use App\Tests\TestCase;

#[CoversClass(Directory::class)]
class DirectoryTest extends TestCase
{
    protected m\MockInterface $ldapManager;
    protected m\MockInterface $config;
    protected Directory $obj;

    public function setUp(): void
    {
        parent::setUp();
        $this->ldapManager = m::mock(LdapManager::class);
        $this->config = m::mock(Config::class);
        $this->obj = new Directory(
            $this->ldapManager,
            $this->config
        );
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->obj);
        unset($this->ldapManager);
        unset($this->config);
    }

    public function testFindByCampusId(): void
    {
        $this->config->shouldReceive('get')->once()->with('ldap_directory_campus_id_property')->andReturn('campusId');
        $this->ldapManager->shouldReceive('search')->with('(campusId=1234)')->andReturn([['id' => 1]]);

        $result = $this->obj->findByCampusId('1234');
        $this->assertSame($result, ['id' => 1]);
    }

    public function testFindByCampusIds(): void
    {
        $this->config->shouldReceive('get')->once()->with('ldap_directory_campus_id_property')->andReturn('campusId');
        $this->ldapManager->shouldReceive('search')
            ->with('(|(campusId=1234)(campusId=1235))')->andReturn([['id' => 1], ['id' => 2]]);

        $result = $this->obj->findByCampusIds([1234, 1235]);
        $this->assertSame($result, [['id' => 1], ['id' => 2]]);
    }

    public function testFindByCampusIdsOnlyUseUnique(): void
    {
        $this->config->shouldReceive('get')->once()->with('ldap_directory_campus_id_property')->andReturn('campusId');
        $this->ldapManager->shouldReceive('search')
            ->with(m::mustBe('(|(campusId=1234)(campusId=1235))'))->andReturn([1]);

        $result = $this->obj->findByCampusIds([1234, 1235, 1234, 1235]);
        $this->assertSame($result, [1]);
    }

    public function testFindByCampusIdsInChunks(): void
    {
        $this->config->shouldReceive('get')->once()->with('ldap_directory_campus_id_property')->andReturn('campusId');
        $ids = [];
        $firstFilters = '(|';
        for ($i = 0; $i < 50; $i++) {
            $ids[] = $i;
            $firstFilters .= "(campusId={$i})";
        }
        $firstFilters .= ')';

        $secondFilters = '(|';
        for ($i = 50; $i < 100; $i++) {
            $ids[] = $i;
            $secondFilters .= "(campusId={$i})";
        }
        $secondFilters .= ')';

        $this->ldapManager->shouldReceive('search')
            ->with($firstFilters)->andReturn([1])->once();
        $this->ldapManager->shouldReceive('search')
            ->with($secondFilters)->andReturn([2])->once();

        $result = $this->obj->findByCampusIds($ids);
        $this->assertSame($result, [1, 2]);
    }

    public function testFind(): void
    {
        $this->setupConfigForSearch();
        $filter = '(&' .
            '(|(mail=a*)(cid=a*)(dn=a*)(f=a*)(l=a*)(m=a*)(pfn=a*)(pmn=a*)(pln=a*))' .
            '(|(mail=b*)(cid=b*)(dn=b*)(f=b*)(l=b*)(m=b*)(pfn=b*)(pmn=b*)(pln=b*))' .
        ')';
        $this->ldapManager->shouldReceive('search')->with($filter)->andReturn([1,2]);

        $result = $this->obj->find(['a', 'b']);
        $this->assertSame($result, [1,2]);
    }

    public function testFindOutputEscaping(): void
    {
        $this->setupConfigForSearch();
        $filter = '(&(|(mail=a\2a*)(cid=a\2a*)(dn=a\2a*)(f=a\2a*)(l=a\2a*)(m=a\2a*)(pfn=a\2a*)(pmn=a\2a*)(pln=a\2a*)))';
        $this->ldapManager->shouldReceive('search')->with($filter)->andReturn([1,2]);

        $result = $this->obj->find(['a*']);
        $this->assertSame($result, [1,2]);
    }

    public function testFindWithDefaultNameFields(): void
    {
        $this->setupConfigForSearch([
            'ldap_directory_first_name_property' => null,
            'ldap_directory_middle_name_property' => null,
            'ldap_directory_last_name_property' => null,
        ]);
        $filter = '(&(|(mail=jj*)(cid=jj*)(dn=jj*)(givenName=jj*)(sn=jj*)(pfn=jj*)(pmn=jj*)(pln=jj*)))';
        $this->ldapManager->shouldReceive('search')->with($filter)->andReturn([1,2]);

        $result = $this->obj->find(['jj']);
        $this->assertSame($result, [1,2]);
    }

    public function testFindWithoutPreferredNameFields(): void
    {
        $this->setupConfigForSearch([
            'ldap_directory_preferred_first_name_property' => null,
            'ldap_directory_preferred_middle_name_property' => null,
            'ldap_directory_preferred_last_name_property' => null,
        ]);
        $filter = '(&(|(mail=jj*)(cid=jj*)(dn=jj*)(f=jj*)(l=jj*)(m=jj*)))';
        $this->ldapManager->shouldReceive('search')->with($filter)->andReturn([1,2]);

        $result = $this->obj->find(['jj']);
        $this->assertSame($result, [1,2]);
    }

    public function testFindByLdapFilter(): void
    {
        $filter = '(one)(two)';
        $this->ldapManager->shouldReceive('search')->with($filter)->andReturn([1,2]);

        $result = $this->obj->findByLdapFilter($filter);
        $this->assertSame($result, [1,2]);
    }

    protected function setupConfigForSearch(array $overrides = []): void
    {
        $defaults = [
            'ldap_directory_preferred_first_name_property' => 'pfn',
            'ldap_directory_preferred_middle_name_property' => 'pmn',
            'ldap_directory_preferred_last_name_property' => 'pln',
            'ldap_directory_campus_id_property' => 'cid',
            'ldap_directory_display_name_property' => 'dn',
            'ldap_directory_first_name_property' => 'f',
            'ldap_directory_middle_name_property' => 'm',
            'ldap_directory_last_name_property' => 'l',
        ];
        foreach (array_merge($defaults, $overrides) as $key => $value) {
            $this->config->shouldReceive('get')->once()
                ->with($key)
                ->andReturn($value);
        }
    }
}
