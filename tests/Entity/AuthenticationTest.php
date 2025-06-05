<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Entity\Authentication;
use App\Entity\UserInterface;
use Mockery as m;

/**
 * Tests for Entity Authentication
 */
#[Group('model')]
#[CoversClass(Authentication::class)]
final class AuthenticationTest extends EntityBase
{
    protected Authentication $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new Authentication();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
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

    public function testSetUsername(): void
    {
        $this->assertNull($this->object->getUsername());
        $this->basicSetTest('username', 'string');
    }

    public function testSetUser(): void
    {
        $this->entitySetTest('user', 'User');
    }

    public function testSetInvalidateTokenIssuedBefore(): void
    {
        $this->basicSetTest('invalidateTokenIssuedBefore', 'datetime');
    }

    public function testPasswordHash(): void
    {
        $this->assertNull($this->object->getPasswordHash());
        $this->basicSetTest('passwordHash', 'string');
    }

    public function testPassword(): void
    {
        $this->assertNull($this->object->getPassword());
        $this->object->setPasswordHash('test');
        $this->assertEquals('test', $this->object->getPassword());
    }

    protected function getObject(): Authentication
    {
        return $this->object;
    }
}
