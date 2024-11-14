<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\MeshQualifier;
use DateTime;

/**
 * Tests for Entity MeshQualifier
 * @group model
 */
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
    /**
     * @covers \App\Entity\MeshQualifier::__construct
     */
    public function testConstructor(): void
    {
        $now = new DateTime();
        $createdAt = $this->object->getCreatedAt();
        $diff = $now->diff($createdAt);
        $this->assertTrue($diff->s < 2);
    }

    /**
     * @covers \App\Entity\MeshQualifier::setName
     * @covers \App\Entity\MeshQualifier::getName
     */
    public function testSetName(): void
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \App\Entity\MeshQualifier::addDescriptor
     */
    public function testAddDescriptor(): void
    {
        $this->entityCollectionAddTest('descriptor', 'MeshDescriptor');
    }

    /**
     * @covers \App\Entity\MeshQualifier::removeDescriptor
     */
    public function testRemoveDescriptor(): void
    {
        $this->entityCollectionRemoveTest('descriptor', 'MeshDescriptor');
    }

    /**
     * @covers \App\Entity\MeshQualifier::getDescriptors
     * @covers \App\Entity\MeshQualifier::setDescriptors
     */
    public function testGetDescriptors(): void
    {
        $this->entityCollectionSetTest('descriptor', 'MeshDescriptor');
    }

    protected function getObject(): MeshQualifier
    {
        return $this->object;
    }
}
