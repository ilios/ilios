<?php
namespace Tests\CoreBundle\Entity;

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

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'title'
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('test');
        $this->validate(0);
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\UserRole::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getUsers());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\UserRole::setTitle
     * @covers \Ilios\CoreBundle\Entity\UserRole::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\UserRole::addUser
     */
    public function testAddUser()
    {
        $this->entityCollectionAddTest('user', 'User', false, false, 'addRole');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\UserRole::removeUser
     */
    public function testRemoveUser()
    {
        $this->entityCollectionRemoveTest('user', 'User', false, false, false, 'removeRole');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\UserRole::getUsers
     */
    public function testGetUsers()
    {
        $this->entityCollectionSetTest('user', 'User', false, false, 'addRole');
    }

    public function testGetRole()
    {
        $this->object->setTitle('test');
        $this->assertEquals('ROLE_test', $this->object->getRole());
    }
}
