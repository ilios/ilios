<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\UserRole;

/**
 * Tests for Entity UserRole
 * @group model
 */
class UserRoleTest extends EntityBase
{
    protected UserRole $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new UserRole();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'title',
        ];
        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('test');
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\UserRole::__construct
     */
    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getUsers());
    }

    /**
     * @covers \App\Entity\UserRole::setTitle
     * @covers \App\Entity\UserRole::getTitle
     */
    public function testSetTitle(): void
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\UserRole::addUser
     */
    public function testAddUser(): void
    {
        $this->entityCollectionAddTest('user', 'User', false, false, 'addRole');
    }

    /**
     * @covers \App\Entity\UserRole::removeUser
     */
    public function testRemoveUser(): void
    {
        $this->entityCollectionRemoveTest('user', 'User', false, false, false, 'removeRole');
    }

    /**
     * @covers \App\Entity\UserRole::getUsers
     */
    public function testGetUsers(): void
    {
        $this->entityCollectionSetTest('user', 'User', false, false, 'addRole');
    }

    protected function getObject(): UserRole
    {
        return $this->object;
    }
}
