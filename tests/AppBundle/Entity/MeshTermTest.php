<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\MeshTerm;
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
     * @covers \AppBundle\Entity\MeshTerm::__construct
     */
    public function testConstructor()
    {
        $now = new \DateTime();
        $createdAt = $this->object->getCreatedAt();
        $this->assertTrue($createdAt instanceof \DateTime);
        $diff = $now->diff($createdAt);
        $this->assertTrue($diff->s < 2);
    }

    /**
     * @covers \AppBundle\Entity\MeshTerm::setName
     * @covers \AppBundle\Entity\MeshTerm::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \AppBundle\Entity\MeshTerm::setLexicalTag
     * @covers \AppBundle\Entity\MeshTerm::getLexicalTag
     */
    public function testSetLexicalTag()
    {
        $this->basicSetTest('lexicalTag', 'string');
    }

    /**
     * @covers \AppBundle\Entity\MeshTerm::setConceptPreferred
     * @covers \AppBundle\Entity\MeshTerm::isConceptPreferred
     */
    public function testSetConceptPreferred()
    {
        $this->booleanSetTest('conceptPreferred');
    }

    /**
     * @covers \AppBundle\Entity\MeshTerm::setRecordPreferred
     * @covers \AppBundle\Entity\MeshTerm::isRecordPreferred
     */
    public function testSetRecordPreferred()
    {
        $this->booleanSetTest('recordPreferred');
    }

    /**
     * @covers \AppBundle\Entity\MeshTerm::setPermuted
     * @covers \AppBundle\Entity\MeshTerm::isPermuted
     */
    public function testSetPermuted()
    {
        $this->booleanSetTest('permuted');
    }

    /**
     * @covers \AppBundle\Entity\MeshTerm::stampUpdate
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
     * @covers \AppBundle\Entity\MeshTerm::addConcept
     */
    public function testAddConcept()
    {
        $this->entityCollectionAddTest('concept', 'MeshConcept');
    }

    /**
     * @covers \AppBundle\Entity\MeshTerm::removeConcept
     */
    public function testRemoveConcept()
    {
        $this->entityCollectionRemoveTest('concept', 'MeshConcept');
    }

    /**
     * @covers \AppBundle\Entity\MeshTerm::getConcepts
     */
    public function getGetConcepts()
    {
        $this->entityCollectionSetTest('concept', 'MeshConcept');
    }
}
