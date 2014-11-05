<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\UserRole;
use Mockery as m;

/**
 * Tests for Model UserRole
 */
class UserRoleTest extends BaseModel
{
    /**
     * @var UserRole
     */
    protected $object;

    /**
     * Instantiate a UserRole object
     */
    protected function setUp()
    {
        $this->object = new UserRole;
    }

    /**
     * @covers Ilios\CoreBundle\Model\UserRole::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getUsers());
    }

    /**
     * @covers Ilios\CoreBundle\Model\UserRole::getUserRoleId
     */
    public function testGetUserRoleId()
    {
        $this->basicGetTest('userRoleId', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\UserRole::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\UserRole::getTitle
     */
    public function testGetTitle()
    {
        $this->basicGetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\UserRole::addUser
     */
    public function testAddUser()
    {
        $this->modelCollectionAddTest('user', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\UserRole::removeUser
     */
    public function testRemoveUser()
    {
        $this->modelCollectionRemoveTest('user', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\UserRole::getUsers
     */
    public function testGetUsers()
    {
        $this->modelCollectionGetTest('user', 'User');
    }
}
