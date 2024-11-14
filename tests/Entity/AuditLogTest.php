<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\AuditLog;

use function method_exists;

/**
 * Tests for Entity AuditLog
 * @group model
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Entity\AuditLog::class)]
class AuditLogTest extends EntityBase
{
    protected AuditLog $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new AuditLog();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'action',
            'objectId',
            'objectClass',
            'valuesChanged',
        ];
        $this->validateNotBlanks($notBlank);

        $this->object->setAction('test');
        $this->object->setObjectId('1');
        $this->object->setObjectClass('test');
        $this->object->setValuesChanged('test');
        $this->validate(0);
    }

    public function testSetAction(): void
    {
        $this->basicSetTest('action', 'string');
    }

    public function testSetObjectIdConvertsIntToString(): void
    {
        $this->object->setObjectId(11);
        $this->assertSame('11', $this->object->getObjectId());
    }

    public function testSetObjectIdString(): void
    {
        $this->basicSetTest('objectId', 'string');
    }

    public function testSetObjectClass(): void
    {
        $this->basicSetTest('objectClass', 'string');
    }

    public function testSetValuesChanged(): void
    {
        $this->basicSetTest('valuesChanged', 'string');
    }

    public function testSetUser(): void
    {
        $this->entitySetTest('user', 'User');
    }

    public function testSetServiceToken(): void
    {
        $this->entitySetTest('serviceToken', 'ServiceToken');
    }

    public function testSetCreatedAt(): void
    {
        $this->basicSetTest('createdAt', 'datetime');
    }

    protected function getObject(): AuditLog
    {
        return $this->object;
    }
}
