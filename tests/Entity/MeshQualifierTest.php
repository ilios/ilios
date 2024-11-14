<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\MeshQualifier;
use DateTime;

/**
 * Tests for Entity MeshQualifier
 * @group model
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Entity\MeshQualifier::class)]
class MeshQualifierTest extends EntityBase
{
    protected MeshQualifier $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new MeshQualifier();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'name',
        ];
        $this->validateNotBlanks($notBlank);

        $this->object->setName('test_name');
        $this->validate(0);
    }
    public function testConstructor(): void
    {
        $now = new DateTime();
        $createdAt = $this->object->getCreatedAt();
        $diff = $now->diff($createdAt);
        $this->assertTrue($diff->s < 2);
    }

    public function testSetName(): void
    {
        $this->basicSetTest('name', 'string');
    }

    public function testAddDescriptor(): void
    {
        $this->entityCollectionAddTest('descriptor', 'MeshDescriptor');
    }

    public function testRemoveDescriptor(): void
    {
        $this->entityCollectionRemoveTest('descriptor', 'MeshDescriptor');
    }

    public function testGetDescriptors(): void
    {
        $this->entityCollectionSetTest('descriptor', 'MeshDescriptor');
    }

    protected function getObject(): MeshQualifier
    {
        return $this->object;
    }
}
