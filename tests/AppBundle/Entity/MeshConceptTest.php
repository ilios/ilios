<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\MeshConcept;
use Mockery as m;

/**
 * Tests for Entity MeshConcept
 */
class MeshConceptTest extends EntityBase
{
    /**
     * @var MeshConcept
     */
    protected $object;

    /**
     * Instantiate a MeshConcept object
     */
    protected function setUp()
    {
        $this->object = new MeshConcept;
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'name',
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setName('test_name');
        $this->validate(0);
    }

    /**
     * @covers \AppBundle\Entity\MeshConcept::__construct
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
     * @covers \AppBundle\Entity\MeshConcept::setName
     * @covers \AppBundle\Entity\MeshConcept::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \AppBundle\Entity\MeshConcept::setPreferred
     * @covers \AppBundle\Entity\MeshConcept::getPreferred
     */
    public function testSetPreferred()
    {
        $this->basicSetTest('preferred', 'string');
    }

    /**
     * @covers \AppBundle\Entity\MeshConcept::setScopeNote
     * @covers \AppBundle\Entity\MeshConcept::getScopeNote
     */
    public function testSetScopeNote()
    {
        $this->basicSetTest('scopeNote', 'string');
    }

    /**
     * @covers \AppBundle\Entity\MeshConcept::setCasn1Name
     */
    public function testSetCasn1Name()
    {
        $this->basicSetTest('casn1Name', 'string');
    }

    /**
     * @covers \AppBundle\Entity\MeshConcept::setRegistryNumber
     * @covers \AppBundle\Entity\MeshConcept::getRegistryNumber
     */
    public function testSetRegistryNumber()
    {
        $this->basicSetTest('registryNumber', 'string');
    }

    /**
     * @covers \AppBundle\Entity\MeshConcept::addTerm
     */
    public function testAddTerm()
    {
        $this->entityCollectionAddTest('term', 'MeshTerm', false, false, 'addConcept');
    }

    /**
     * @covers \AppBundle\Entity\MeshConcept::removeTerm
     */
    public function testRemoveTerm()
    {
        $this->entityCollectionRemoveTest('term', 'MeshTerm', false, false, false, 'removeConcept');
    }

    /**
     * @covers \AppBundle\Entity\MeshConcept::setTerms
     * @covers \AppBundle\Entity\MeshConcept::getTerms
     */
    public function testGetTerms()
    {
        $this->entityCollectionSetTest('term', 'MeshTerm', false, false, 'addConcept');
    }

    /**
     * @covers \AppBundle\Entity\MeshConcept::addDescriptor
     */
    public function testAddDescriptor()
    {
        $this->entityCollectionAddTest('descriptor', 'MeshDescriptor');
    }

    /**
     * @covers \AppBundle\Entity\MeshConcept::removeDescriptor
     */
    public function testRemoveDescriptor()
    {
        $this->entityCollectionRemoveTest('descriptor', 'MeshDescriptor');
    }

    /**
     * @covers \AppBundle\Entity\MeshConcept::setDescriptors
     * @covers \AppBundle\Entity\MeshConcept::getDescriptors
     */
    public function testGetDescriptors()
    {
        $this->entityCollectionSetTest('descriptor', 'MeshDescriptor');
    }

    /**
     * @covers \AppBundle\Entity\MeshConcept::stampUpdate
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
}
