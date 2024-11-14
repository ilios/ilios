<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\PendingUserUpdate;
use App\Entity\User;

/**
 * Tests for Entity PendingUserUpdate
 * @group model
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Entity\PendingUserUpdate::class)]
class PendingUserUpdateTest extends EntityBase
{
    protected PendingUserUpdate $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new PendingUserUpdate();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'type',
            'property',
            'value',
        ];
        $this->object->setUser(new User());
        $this->validateNotBlanks($notBlank);

        $this->object->setType('test');
        $this->object->setProperty('test');
        $this->object->setValue('test');
        $this->validate(0);
    }

    public function testNotNullValidation(): void
    {
        $notNull = [
            'user',
        ];

        $this->object->setType('test');
        $this->object->setProperty('test');
        $this->object->setValue('test');
        $this->validateNotNulls($notNull);

        $this->object->setUser(new User());
        $this->validate(0);
    }

    public function testSetType(): void
    {
        $this->basicSetTest('type', 'string');
    }

    public function testSetProperty(): void
    {
        $this->basicSetTest('property', 'string');
    }

    public function testSetValue(): void
    {
        $this->basicSetTest('value', 'string');
    }

    public function testSetUser(): void
    {
        $this->entitySetTest('user', 'User');
    }

    protected function getObject(): PendingUserUpdate
    {
        return $this->object;
    }
}
