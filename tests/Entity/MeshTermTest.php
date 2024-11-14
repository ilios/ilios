<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\MeshTerm;
use DateTime;

/**
 * Tests for Entity MeshTerm
 * @group model
 */
class MeshTermTest extends EntityBase
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


    /**
     * @covers \App\Entity\MeshTerm::__construct
     */
    public function testConstructor(): void
    {
        $now = new DateTime();
        $createdAt = $this->object->getCreatedAt();
        $diff = $now->diff($createdAt);
        $this->assertTrue($diff->s < 2);
    }

    /**
     * @covers \App\Entity\MeshTerm::setName
     * @covers \App\Entity\MeshTerm::getName
     */
    public function testSetName(): void
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \App\Entity\MeshTerm::setLexicalTag
     * @covers \App\Entity\MeshTerm::getLexicalTag
     */
    public function testSetLexicalTag(): void
    {
        $this->basicSetTest('lexicalTag', 'string');
    }

    /**
     * @covers \App\Entity\MeshTerm::setConceptPreferred
     * @covers \App\Entity\MeshTerm::isConceptPreferred
     */
    public function testSetConceptPreferred(): void
    {
        $this->booleanSetTest('conceptPreferred');
    }

    /**
     * @covers \App\Entity\MeshTerm::setRecordPreferred
     * @covers \App\Entity\MeshTerm::isRecordPreferred
     */
    public function testSetRecordPreferred(): void
    {
        $this->booleanSetTest('recordPreferred');
    }

    /**
     * @covers \App\Entity\MeshTerm::setPermuted
     * @covers \App\Entity\MeshTerm::isPermuted
     */
    public function testSetPermuted(): void
    {
        $this->booleanSetTest('permuted');
    }

    /**
     * @covers \App\Entity\MeshTerm::addConcept
     */
    public function testAddConcept(): void
    {
        $this->entityCollectionAddTest('concept', 'MeshConcept');
    }

    /**
     * @covers \App\Entity\MeshTerm::removeConcept
     */
    public function testRemoveConcept(): void
    {
        $this->entityCollectionRemoveTest('concept', 'MeshConcept');
    }

    /**
     * @covers \App\Entity\MeshTerm::getConcepts
     */
    public function testGetConcepts(): void
    {
        $this->entityCollectionSetTest('concept', 'MeshConcept');
    }

    protected function getObject(): MeshTerm
    {
        return $this->object;
    }
}
