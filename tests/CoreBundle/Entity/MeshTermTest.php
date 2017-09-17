<?php
namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\MeshTerm;
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
     * @covers \Ilios\CoreBundle\Entity\MeshTerm::__construct
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
     * @covers \Ilios\CoreBundle\Entity\MeshTerm::setName
     * @covers \Ilios\CoreBundle\Entity\MeshTerm::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshTerm::setLexicalTag
     * @covers \Ilios\CoreBundle\Entity\MeshTerm::getLexicalTag
     */
    public function testSetLexicalTag()
    {
        $this->basicSetTest('lexicalTag', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshTerm::setConceptPreferred
     * @covers \Ilios\CoreBundle\Entity\MeshTerm::isConceptPreferred
     */
    public function testSetConceptPreferred()
    {
        $this->booleanSetTest('conceptPreferred');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshTerm::setRecordPreferred
     * @covers \Ilios\CoreBundle\Entity\MeshTerm::isRecordPreferred
     */
    public function testSetRecordPreferred()
    {
        $this->booleanSetTest('recordPreferred');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshTerm::setPermuted
     * @covers \Ilios\CoreBundle\Entity\MeshTerm::isPermuted
     */
    public function testSetPermuted()
    {
        $this->booleanSetTest('permuted');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshTerm::stampUpdate
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
     * @covers \Ilios\CoreBundle\Entity\MeshTerm::addConcept
     */
    public function testAddConcept()
    {
        $this->entityCollectionAddTest('concept', 'MeshConcept');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshTerm::removeConcept
     */
    public function testRemoveConcept()
    {
        $this->entityCollectionRemoveTest('concept', 'MeshConcept');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshTerm::getConcepts
     */
    public function getGetConcepts()
    {
        $this->entityCollectionSetTest('concept', 'MeshConcept');
    }
}
