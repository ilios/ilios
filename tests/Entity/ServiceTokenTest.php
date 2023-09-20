<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\ServiceToken;
use DateTime;

/**
 * @group model
 * @coversDefaultClass \App\Entity\ServiceToken
 */
class ServiceTokenTest extends EntityBase
{
    /** @var ServiceToken */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new ServiceToken();
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'description',
            'createdAt',
            'expiresAt',
        ];
        $this->validateNotBlanks($notBlank);

        $this->object->setDescription('test');
        $this->object->setCreatedAt(new DateTime());
        $this->object->setExpiresAt(new DateTime());
        $this->validate(0);
    }

    /**
     * @covers ::__construct
     */
    public function testConstructor(): void
    {
        $this->assertEmpty($this->object->getAuditLogs()->toArray());
        $this->assertTrue($this->object->isEnabled());
    }

    /**
     * @covers ::setDescription
     * @covers ::getDescription
     */
    public function testSetDescription(): void
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers ::setCreatedAt
     * @covers ::getCreatedAt
     */
    public function testSetCreatedAt(): void
    {
        $this->basicSetTest('createdAt', 'datetime');
    }

    /**
     * @covers ::setExpiresAt
     * @covers ::getExpiresAt
     */
    public function testSetExpiresAt(): void
    {
        $this->basicSetTest('expiresAt', 'datetime');
    }

    /**
     * @covers ::addAuditLog
     */
    public function testAddAuditLog()
    {
        $this->entityCollectionAddTest('auditLog', 'AuditLog');
    }

    /**
     * @covers ::removeAuditLog
     */
    public function testRemoveAuditLog()
    {
        $this->entityCollectionRemoveTest('auditLog', 'AuditLog');
    }

    /**
     * @covers ::setAuditLogs
     * @covers ::getAuditLogs
     */
    public function testSetAuditLogs()
    {
        $this->entityCollectionSetTest('auditLog', 'AuditLog');
    }
}
