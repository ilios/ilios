<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\Permission;
use Mockery as m;

/**
 * Tests for Model Permission
 */
class PermissionTest extends ModelBase
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
     * @covers Ilios\CoreBundle\Model\Permission::getPermissionId
     */
    public function testGetPermissionId()
    {
        $this->basicGetTest('permissionId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Permission::setUserId
     */
    public function testSetUserId()
    {
        $this->basicSetTest('userId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Permission::getUserId
     */
    public function testGetUserId()
    {
        $this->basicGetTest('userId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Permission::setCanRead
     */
    public function testSetCanRead()
    {
        $this->basicSetTest('canRead', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Permission::getCanRead
     */
    public function testGetCanRead()
    {
        $this->basicGetTest('canRead', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Permission::setCanWrite
     */
    public function testSetCanWrite()
    {
        $this->basicSetTest('canWrite', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Permission::getCanWrite
     */
    public function testGetCanWrite()
    {
        $this->basicGetTest('canWrite', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Permission::setTableRowId
     */
    public function testSetTableRowId()
    {
        $this->basicSetTest('tableRowId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Permission::getTableRowId
     */
    public function testGetTableRowId()
    {
        $this->basicGetTest('tableRowId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Permission::setTableName
     */
    public function testSetTableName()
    {
        $this->basicSetTest('tableName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Permission::getTableName
     */
    public function testGetTableName()
    {
        $this->basicGetTest('tableName', 'string');
    }
}
