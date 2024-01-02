<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\PendingUserUpdate;
use App\Entity\User;

/**
 * Tests for Entity PendingUserUpdate
 * @group model
 */
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

    /**
     * @covers \App\Entity\PendingUserUpdate::setType
     * @covers \App\Entity\PendingUserUpdate::getType
     */
    public function testSetType(): void
    {
        $this->basicSetTest('type', 'string');
    }

    /**
     * @covers \App\Entity\PendingUserUpdate::setProperty
     * @covers \App\Entity\PendingUserUpdate::getProperty
     */
    public function testSetProperty(): void
    {
        $this->basicSetTest('property', 'string');
    }

    /**
     * @covers \App\Entity\PendingUserUpdate::setValue
     * @covers \App\Entity\PendingUserUpdate::getValue
     */
    public function testSetValue(): void
    {
        $this->basicSetTest('value', 'string');
    }

    /**
     * @covers \App\Entity\PendingUserUpdate::setUser
     * @covers \App\Entity\PendingUserUpdate::getUser
     */
    public function testSetUser(): void
    {
        $this->entitySetTest('user', 'User');
    }

    protected function getObject(): PendingUserUpdate
    {
        return $this->object;
    }
}
