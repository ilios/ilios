<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\UserRole;

/**
 * Tests for Entity UserRole
 */
#[\PHPUnit\Framework\Attributes\Group('model')]
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Entity\UserRole::class)]
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

    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getUsers());
    }

    public function testSetTitle(): void
    {
        $this->basicSetTest('title', 'string');
    }

    public function testAddUser(): void
    {
        $this->entityCollectionAddTest('user', 'User', false, false, 'addRole');
    }

    public function testRemoveUser(): void
    {
        $this->entityCollectionRemoveTest('user', 'User', false, false, false, 'removeRole');
    }

    public function testGetUsers(): void
    {
        $this->entityCollectionSetTest('user', 'User', false, false, 'addRole');
    }

    protected function getObject(): UserRole
    {
        return $this->object;
    }
}
