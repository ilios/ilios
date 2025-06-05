<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Entity\MeshConcept;
use DateTime;

/**
 * Tests for Entity MeshConcept
 */
#[Group('model')]
#[CoversClass(MeshConcept::class)]
final class MeshConceptTest extends EntityBase
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

    public function testSetPreferred(): void
    {
        $this->basicSetTest('preferred', 'boolean');
    }

    public function testSetScopeNote(): void
    {
        $this->basicSetTest('scopeNote', 'string');
    }

    public function testSetCasn1Name(): void
    {
        $this->basicSetTest('casn1Name', 'string');
    }

    public function testAddTerm(): void
    {
        $this->entityCollectionAddTest('term', 'MeshTerm', false, false, 'addConcept');
    }

    public function testRemoveTerm(): void
    {
        $this->entityCollectionRemoveTest('term', 'MeshTerm', false, false, false, 'removeConcept');
    }

    public function testGetTerms(): void
    {
        $this->entityCollectionSetTest('term', 'MeshTerm', false, false, 'addConcept');
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

    protected function getObject(): MeshConcept
    {
        return $this->object;
    }
}
