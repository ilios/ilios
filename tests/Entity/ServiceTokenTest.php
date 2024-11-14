<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\ServiceToken;
use DateTime;

#[\PHPUnit\Framework\Attributes\Group('model')]
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Entity\ServiceToken::class)]
class ServiceTokenTest extends EntityBase
{
    protected ServiceToken $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new ServiceToken();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
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

    public function testConstructor(): void
    {
        $this->assertEmpty($this->object->getAuditLogs()->toArray());
        $this->assertTrue($this->object->isEnabled());
    }

    public function testSetDescription(): void
    {
        $this->basicSetTest('description', 'string');
    }

    public function testSetCreatedAt(): void
    {
        $this->basicSetTest('createdAt', 'datetime');
    }

    public function testSetExpiresAt(): void
    {
        $this->basicSetTest('expiresAt', 'datetime');
    }

    public function testAddAuditLog(): void
    {
        $this->entityCollectionAddTest('auditLog', 'AuditLog');
    }

    public function testRemoveAuditLog(): void
    {
        $this->entityCollectionRemoveTest('auditLog', 'AuditLog');
    }

    public function testSetAuditLogs(): void
    {
        $this->entityCollectionSetTest('auditLog', 'AuditLog');
    }

    protected function getObject(): ServiceToken
    {
        return $this->object;
    }
}
