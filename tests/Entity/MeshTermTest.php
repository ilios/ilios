<?php
namespace App\Tests\Entity;

use App\Entity\MeshTerm;
use Mockery as m;

/**
 * Tests for Entity MeshTerm
 */
class MeshTermTest extends EntityBase
{
    /**
     * @var MeshTerm
     */
    protected $object;

    /**
     * Instantiate a MeshTerm object
     */
    protected function setUp()
    {
        $this->object = new MeshTerm;
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'name',
            'meshTermUid'
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setName('test up to 192 in length search string');
        $this->object->setMeshTermUid('boots!');
        $this->validate(0);
    }


    /**
     * @covers \App\Entity\MeshTerm::__construct
     */
    public function testConstructor()
    {
        $now = \DateTime::createFromFormat('U', time());
        $createdAt = $this->object->getCreatedAt();
        $this->assertTrue($createdAt instanceof \DateTime);
        $diff = $now->diff($createdAt);
        $this->assertTrue($diff->s < 2);
    }

    /**
     * @covers \App\Entity\MeshTerm::setName
     * @covers \App\Entity\MeshTerm::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \App\Entity\MeshTerm::setLexicalTag
     * @covers \App\Entity\MeshTerm::getLexicalTag
     */
    public function testSetLexicalTag()
    {
        $this->basicSetTest('lexicalTag', 'string');
    }

    /**
     * @covers \App\Entity\MeshTerm::setConceptPreferred
     * @covers \App\Entity\MeshTerm::isConceptPreferred
     */
    public function testSetConceptPreferred()
    {
        $this->booleanSetTest('conceptPreferred');
    }

    /**
     * @covers \App\Entity\MeshTerm::setRecordPreferred
     * @covers \App\Entity\MeshTerm::isRecordPreferred
     */
    public function testSetRecordPreferred()
    {
        $this->booleanSetTest('recordPreferred');
    }

    /**
     * @covers \App\Entity\MeshTerm::setPermuted
     * @covers \App\Entity\MeshTerm::isPermuted
     */
    public function testSetPermuted()
    {
        $this->booleanSetTest('permuted');
    }

    /**
     * @covers \App\Entity\MeshTerm::stampUpdate
     */
    public function testStampUpdate()
    {
        $now = new \DateTime();
        $this->object->stampUpdate();
        $updatedAt = $this->object->getUpdatedAt();
        $this->assertTrue($updatedAt instanceof \DateTime);
        $diff = $now->diff($updatedAt);
        $this->assertTrue($diff->s < 2);
    }

    /**
     * @covers \App\Entity\MeshTerm::addConcept
     */
    public function testAddConcept()
    {
        $this->entityCollectionAddTest('concept', 'MeshConcept');
    }

    /**
     * @covers \App\Entity\MeshTerm::removeConcept
     */
    public function testRemoveConcept()
    {
        $this->entityCollectionRemoveTest('concept', 'MeshConcept');
    }

    /**
     * @covers \App\Entity\MeshTerm::getConcepts
     */
    public function getGetConcepts()
    {
        $this->entityCollectionSetTest('concept', 'MeshConcept');
    }
}
