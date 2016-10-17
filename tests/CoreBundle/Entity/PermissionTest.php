<?php
namespace Tests\CoreBundle\Entity;

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

    // some bool set up as NotNull() some as NotBlank() - need to discuss and standardize for the
    // NotBlank() tests

    /**
     * @covers \Ilios\CoreBundle\Entity\Permission::setCanRead
     * @covers \Ilios\CoreBundle\Entity\Permission::hasCanRead
     */
    public function testSetCanRead()
    {
        $this->booleanSetTest('canRead', false);
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Permission::setCanWrite
     * @covers \Ilios\CoreBundle\Entity\Permission::hasCanWrite
     */
    public function testSetCanWrite()
    {
        $this->booleanSetTest('canWrite', false);
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Permission::setTableName
     * @covers \Ilios\CoreBundle\Entity\Permission::getTableName
     */
    public function testSetTableName()
    {
        $this->basicSetTest('tableName', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Permission::setUser
     * @covers \Ilios\CoreBundle\Entity\Permission::getUser
     */
    public function testSetUser()
    {
        $this->entitySetTest('user', 'User');
    }
}
