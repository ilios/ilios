<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Authentication;
use App\Entity\UserInterface;
use Mockery as m;

/**
 * Tests for Entity Authentication
 * @group model
 */
class AuthenticationTest extends EntityBase
{
    protected function setUp(): void
    {
        $this->object = new Authentication();
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'user',
        ];
        $this->validateNotBlanks($notBlank);

        $this->object->setUser(m::mock(UserInterface::class));
        $this->object->setUsername('');
        $this->object->setPasswordHash('');
        $this->validate(0);
        $this->object->setUsername('test');
        $this->object->setPasswordHash('test');
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\Authentication::setUsername
     * @covers \App\Entity\Authentication::getUsername
     */
    public function testSetUsername(): void
    {
        $this->assertNull($this->object->getUsername());
        $this->basicSetTest('username', 'string');
    }

    /**
     * @covers \App\Entity\Authentication::setUser
     * @covers \App\Entity\Authentication::getUser
     */
    public function testSetUser(): void
    {
        $this->entitySetTest('user', 'User');
    }

    /**
     * @covers \App\Entity\Authentication::setInvalidateTokenIssuedBefore
     * @covers \App\Entity\Authentication::getInvalidateTokenIssuedBefore
     */
    public function testSetInvalidateTokenIssuedBefore(): void
    {
        $this->basicSetTest('invalidateTokenIssuedBefore', 'datetime');
    }

    /**
     * @covers \App\Entity\Authentication::setPasswordHash
     * @covers \App\Entity\Authentication::getPasswordHash
     */
    public function testPasswordHash(): void
    {
        $this->assertNull($this->object->getPasswordHash());
        $this->basicSetTest('passwordHash', 'string');
    }

    /**
     * @covers \App\Entity\Authentication::getPassword
     */
    public function testPassword(): void
    {
        $this->assertNull($this->object->getPassword());
        $this->object->setPasswordHash('test');
        $this->assertEquals('test', $this->object->getPassword());
    }
}
