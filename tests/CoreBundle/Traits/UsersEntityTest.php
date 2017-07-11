<?php
namespace Tests\CoreBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\User;
use Ilios\CoreBundle\Traits\UsersEntity;
use Mockery as m;
use Tests\CoreBundle\TestCase;

/**
 * @coversDefaultClass \Ilios\CoreBundle\Traits\UsersEntity
 */

class UsersEntityTest extends TestCase
{
    /**
     * @var UsersEntity
     */
    private $traitObject;
    public function setUp()
    {
        $traitName = UsersEntity::class;
        $this->traitObject = $this->getObjectForTrait($traitName);
    }

    public function tearDown()
    {
        unset($this->object);
    }

    /**
     * @covers ::setUsers
     */
    public function testSetUsers()
    {
        $collection = new ArrayCollection();
        $collection->add(m::mock(User::class));
        $collection->add(m::mock(User::class));
        $collection->add(m::mock(User::class));

        $this->traitObject->setUsers($collection);
        $this->assertEquals($collection, $this->traitObject->getUsers());
    }

    /**
     * @covers ::removeUser
     */
    public function testRemoveUser()
    {
        $collection = new ArrayCollection();
        $one = m::mock(User::class);
        $two = m::mock(User::class);
        $collection->add($one);
        $collection->add($two);

        $this->traitObject->setUsers($collection);
        $this->traitObject->removeUser($one);
        $users = $this->traitObject->getUsers();
        $this->assertEquals(1, $users->count());
        $this->assertEquals($two, $users->first());
    }
}
