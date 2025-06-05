<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Entity\MeshTerm;
use DateTime;

/**
 * Tests for Entity MeshTerm
 */
#[Group('model')]
#[CoversClass(MeshTerm::class)]
final class MeshTermTest extends EntityBase
{
    protected MeshTerm $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new MeshTerm();
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
            'meshTermUid',
        ];
        $this->validateNotBlanks($notBlank);

        $this->object->setName('test up to 192 in length search string');
        $this->object->setMeshTermUid('boots!');
        $this->object->setLexicalTag('');
        $this->validate(0);
        $this->object->setLexicalTag('test');
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

    public function testSetLexicalTag(): void
    {
        $this->basicSetTest('lexicalTag', 'string');
    }

    public function testSetConceptPreferred(): void
    {
        $this->booleanSetTest('conceptPreferred');
    }

    public function testSetRecordPreferred(): void
    {
        $this->booleanSetTest('recordPreferred');
    }

    public function testSetPermuted(): void
    {
        $this->booleanSetTest('permuted');
    }

    public function testAddConcept(): void
    {
        $this->entityCollectionAddTest('concept', 'MeshConcept');
    }

    public function testRemoveConcept(): void
    {
        $this->entityCollectionRemoveTest('concept', 'MeshConcept');
    }

    public function testGetConcepts(): void
    {
        $this->entityCollectionSetTest('concept', 'MeshConcept');
    }

    protected function getObject(): MeshTerm
    {
        return $this->object;
    }
}
