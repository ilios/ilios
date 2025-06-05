<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\User;
use App\Traits\UsersEntity;
use Mockery as m;
use App\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversTrait;

#[CoversTrait(UsersEntity::class)]
final class UsersEntityTest extends TestCase
{
    private object $traitObject;
    public function setUp(): void
    {
        parent::setUp();
        $this->traitObject = new class {
            use UsersEntity;
        };
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->traitObject);
    }

    public function testSetUsers(): void
    {
        $collection = new ArrayCollection();
        $collection->add(m::mock(User::class));
        $collection->add(m::mock(User::class));
        $collection->add(m::mock(User::class));

        $this->traitObject->setUsers($collection);
        $this->assertEquals($collection, $this->traitObject->getUsers());
    }

    public function testRemoveUser(): void
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

    public function testAddUser(): void
    {
        $this->traitObject->setUsers(new ArrayCollection());
        $this->assertCount(0, $this->traitObject->getUsers());

        $one = m::mock(User::class);
        $this->traitObject->addUser($one);
        $this->assertCount(1, $this->traitObject->getUsers());
        $this->assertEquals($one, $this->traitObject->getUsers()->first());
        // duplicate prevention check
        $this->traitObject->addUser($one);
        $this->assertCount(1, $this->traitObject->getUsers());
        $this->assertEquals($one, $this->traitObject->getUsers()->first());

        $two = m::mock(User::class);
        $this->traitObject->addUser($two);
        $this->assertCount(2, $this->traitObject->getUsers());
        $this->assertEquals($one, $this->traitObject->getUsers()->first());
        $this->assertEquals($two, $this->traitObject->getUsers()->last());
    }
}
