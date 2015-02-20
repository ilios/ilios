<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\Permission;
use Mockery as m;

/**
 * Tests for Entity Permission
 */
class PermissionTest extends EntityBase
{
    /**
     * @var Permission
     */
    protected $object;

    /**
     * Instantiate a Permission object
     */
    protected function setUp()
    {
        $this->object = new Permission;
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Permission::hasCanRead
     */
    public function testSetCanRead()
    {
        $this->booleanSetTest('canRead', false);
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Permission::hasCanWrite
     */
    public function testSetCanWrite()
    {
        $this->booleanSetTest('canWrite', false);
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Permission::setTableName
     */
    public function testSetTableName()
    {
        $this->basicSetTest('tableName', 'string');
    }
}
