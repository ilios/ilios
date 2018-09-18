<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\UserRole;
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
     * @covers \AppBundle\Entity\UserRole::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getUsers());
    }

    /**
     * @covers \AppBundle\Entity\UserRole::setTitle
     * @covers \AppBundle\Entity\UserRole::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \AppBundle\Entity\UserRole::addUser
     */
    public function testAddUser()
    {
        $this->entityCollectionAddTest('user', 'User', false, false, 'addRole');
    }

    /**
     * @covers \AppBundle\Entity\UserRole::removeUser
     */
    public function testRemoveUser()
    {
        $this->entityCollectionRemoveTest('user', 'User', false, false, false, 'removeRole');
    }

    /**
     * @covers \AppBundle\Entity\UserRole::getUsers
     */
    public function testGetUsers()
    {
        $this->entityCollectionSetTest('user', 'User', false, false, 'addRole');
    }
}
