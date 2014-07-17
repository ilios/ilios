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
     * @covers Ilios\CoreBundle\Entity\Permission::getPermissionId
     */
    public function testGetPermissionId()
    {
        $this->basicGetTest('permissionId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Permission::setUserId
     */
    public function testSetUserId()
    {
        $this->basicSetTest('userId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Permission::getUserId
     */
    public function testGetUserId()
    {
        $this->basicGetTest('userId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Permission::setCanRead
     */
    public function testSetCanRead()
    {
        $this->basicSetTest('canRead', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Permission::getCanRead
     */
    public function testGetCanRead()
    {
        $this->basicGetTest('canRead', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Permission::setCanWrite
     */
    public function testSetCanWrite()
    {
        $this->basicSetTest('canWrite', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Permission::getCanWrite
     */
    public function testGetCanWrite()
    {
        $this->basicGetTest('canWrite', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Permission::setTableRowId
     */
    public function testSetTableRowId()
    {
        $this->basicSetTest('tableRowId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Permission::getTableRowId
     */
    public function testGetTableRowId()
    {
        $this->basicGetTest('tableRowId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Permission::setTableName
     */
    public function testSetTableName()
    {
        $this->basicSetTest('tableName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Permission::getTableName
     */
    public function testGetTableName()
    {
        $this->basicGetTest('tableName', 'string');
    }
}
