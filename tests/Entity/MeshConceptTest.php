<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\MeshConcept;
use DateTime;

/**
 * Tests for Entity MeshConcept
 * @group model
 */
class MeshConceptTest extends EntityBase
{
    protected MeshConcept $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new MeshConcept();
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
        $this->object->setScopeNote('');
        $this->object->setCasn1Name('');
        $this->object->setRegistryNumber('');
        $this->validate(0);
        $this->object->setScopeNote('test');
        $this->object->setCasn1Name('test');
        $this->object->setRegistryNumber('test');
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\MeshConcept::__construct
     */
    public function testConstructor(): void
    {
        $now = new DateTime();
        $createdAt = $this->object->getCreatedAt();
        $diff = $now->diff($createdAt);
        $this->assertTrue($diff->s < 2);
    }

    /**
     * @covers \App\Entity\MeshConcept::setName
     * @covers \App\Entity\MeshConcept::getName
     */
    public function testSetName(): void
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \App\Entity\MeshConcept::setPreferred
     * @covers \App\Entity\MeshConcept::getPreferred
     */
    public function testSetPreferred(): void
    {
        $this->basicSetTest('preferred', 'boolean');
    }

    /**
     * @covers \App\Entity\MeshConcept::setScopeNote
     * @covers \App\Entity\MeshConcept::getScopeNote
     */
    public function testSetScopeNote(): void
    {
        $this->basicSetTest('scopeNote', 'string');
    }

    /**
     * @covers \App\Entity\MeshConcept::setCasn1Name
     */
    public function testSetCasn1Name(): void
    {
        $this->basicSetTest('casn1Name', 'string');
    }

    /**
     * @covers \App\Entity\MeshConcept::setRegistryNumber
     * @covers \App\Entity\MeshConcept::getRegistryNumber
     */
    public function testSetRegistryNumber(): void
    {
        $this->basicSetTest('registryNumber', 'string');
    }

    /**
     * @covers \App\Entity\MeshConcept::addTerm
     */
    public function testAddTerm(): void
    {
        $this->entityCollectionAddTest('term', 'MeshTerm', false, false, 'addConcept');
    }

    /**
     * @covers \App\Entity\MeshConcept::removeTerm
     */
    public function testRemoveTerm(): void
    {
        $this->entityCollectionRemoveTest('term', 'MeshTerm', false, false, false, 'removeConcept');
    }

    /**
     * @covers \App\Entity\MeshConcept::setTerms
     * @covers \App\Entity\MeshConcept::getTerms
     */
    public function testGetTerms(): void
    {
        $this->entityCollectionSetTest('term', 'MeshTerm', false, false, 'addConcept');
    }

    /**
     * @covers \App\Entity\MeshConcept::addDescriptor
     */
    public function testAddDescriptor(): void
    {
        $this->entityCollectionAddTest('descriptor', 'MeshDescriptor');
    }

    /**
     * @covers \App\Entity\MeshConcept::removeDescriptor
     */
    public function testRemoveDescriptor(): void
    {
        $this->entityCollectionRemoveTest('descriptor', 'MeshDescriptor');
    }

    /**
     * @covers \App\Entity\MeshConcept::setDescriptors
     * @covers \App\Entity\MeshConcept::getDescriptors
     */
    public function testGetDescriptors(): void
    {
        $this->entityCollectionSetTest('descriptor', 'MeshDescriptor');
    }

    protected function getObject(): MeshConcept
    {
        return $this->object;
    }
}
