<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Authentication;
use Mockery as m;

/**
 * Tests for Entity Authentication
 * @group model
 */
class AuthenticationTest extends EntityBase
{
    /**
     * @var Authentication
     */
    protected $object;

    /**
     * Instantiate a Authentication object
     */
    protected function setUp(): void
    {
        $this->object = new Authentication();
    }

    /**
     * @covers \App\Entity\Authentication::setUsername
     * @covers \App\Entity\Authentication::getUsername
     */
    public function testSetUsername()
    {
        $this->assertNull($this->object->getUsername());
        $this->basicSetTest('username', 'string');
    }

    /**
     * @covers \App\Entity\Authentication::setUser
     * @covers \App\Entity\Authentication::getUser
     */
    public function testSetUser()
    {
        $this->entitySetTest('user', 'User');
    }

    /**
     * @covers \App\Entity\Authentication::setInvalidateTokenIssuedBefore
     * @covers \App\Entity\Authentication::getInvalidateTokenIssuedBefore
     */
    public function testSetInvalidateTokenIssuedBefore()
    {
        $this->basicSetTest('invalidateTokenIssuedBefore', 'datetime');
    }

    /**
     * @covers \App\Entity\Authentication::setPasswordHash
     * @covers \App\Entity\Authentication::getPasswordHash
     */
    public function testPasswordHash()
    {
        $this->assertNull($this->object->getPasswordHash());
        $this->basicSetTest('passwordHash', 'string');
    }

    /**
     * @covers \App\Entity\Authentication::getPassword
     */
    public function testPassword()
    {
        $this->assertNull($this->object->getPassword());
        $this->object->setPasswordHash('test');
        $this->assertEquals('test', $this->object->getPassword());
    }
}
