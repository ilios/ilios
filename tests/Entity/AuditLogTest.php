<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\AuditLog;

use function method_exists;

/**
 * Tests for Entity AuditLog
 * @group model
 * @coversDefaultClass \App\Entity\AuditLog
 */
class AuditLogTest extends EntityBase
{
    protected function setUp(): void
    {
        $this->object = new AuditLog();
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'action',
            'objectId',
            'objectClass',
            'valuesChanged'
        ];
        $this->validateNotBlanks($notBlank);

        $this->object->setAction('test');
        $this->object->setObjectId('1');
        $this->object->setObjectClass('test');
        $this->object->setValuesChanged('test');
        $this->validate(0);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $this->assertNotEmpty($this->object->getCreatedAt());
    }

    /**
     * @covers ::setAction
     * @covers ::getAction
     */
    public function testSetAction(): void
    {
        $this->basicSetTest('action', 'string');
    }

    /**
     * @covers ::setObjectId
     * @covers ::getObjectId
     */
    public function testSetObjectIdConvertsIntToString(): void
    {
        $this->assertTrue(method_exists($this->object, 'setObjectId'));
        $this->assertTrue(method_exists($this->object, 'getObjectId'));
        $this->object->setObjectId(11);
        $this->assertSame('11', $this->object->getObjectId());
    }

    /**
     * @covers ::setObjectId
     * @covers ::getObjectId
     */
    public function testSetObjectIdString(): void
    {
        $this->basicSetTest('objectId', 'string');
    }

    /**
     * @covers ::setObjectClass
     * @covers ::getObjectClass
     */
    public function testSetObjectClass(): void
    {
        $this->basicSetTest('objectClass', 'string');
    }

    /**
     * @covers ::setValuesChanged
     * @covers ::getValuesChanged
     */
    public function testSetValuesChanged(): void
    {
        $this->basicSetTest('valuesChanged', 'string');
    }

    /**
     * @covers ::setUser
     * @covers ::getUser
     */
    public function testSetUser(): void
    {
        $this->entitySetTest('user', 'User');
    }

    /**
     * @covers ::setServiceToken
     * @covers ::getServiceToken
     */
    public function testSetServiceToken(): void
    {
        $this->entitySetTest('serviceToken', 'ServiceToken');
    }

    /**
     * @covers ::setCreatedAt
     * @covers ::getCreatedAt
     */
    public function testSetCreatedAt(): void
    {
        $this->basicSetTest('createdAt', 'datetime');
    }
}
