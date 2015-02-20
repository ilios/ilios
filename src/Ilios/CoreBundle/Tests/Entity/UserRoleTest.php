<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\UserRole;
use Mockery as m;

/**
 * Tests for Entity UserRole
 */
class UserRoleTest extends EntityBase
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
     * @covers Ilios\CoreBundle\Entity\UserRole::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getUsers());
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserRole::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserRole::addUser
     */
    public function testAddUser()
    {
        $this->entityCollectionAddTest('user', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserRole::removeUser
     */
    public function testRemoveUser()
    {
        $this->entityCollectionRemoveTest('user', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserRole::getUsers
     */
    public function testGetUsers()
    {
        $this->entityCollectionSetTest('user', 'User');
    }
}
