<?php
namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\MeshConcept;
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
     * @covers \Ilios\CoreBundle\Entity\MeshConcept::__construct
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
     * @covers \Ilios\CoreBundle\Entity\MeshConcept::setName
     * @covers \Ilios\CoreBundle\Entity\MeshConcept::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshConcept::setPreferred
     * @covers \Ilios\CoreBundle\Entity\MeshConcept::getPreferred
     */
    public function testSetPreferred()
    {
        $this->basicSetTest('preferred', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshConcept::setScopeNote
     * @covers \Ilios\CoreBundle\Entity\MeshConcept::getScopeNote
     */
    public function testSetScopeNote()
    {
        $this->basicSetTest('scopeNote', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshConcept::setCasn1Name
     */
    public function testSetCasn1Name()
    {
        $this->basicSetTest('casn1Name', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshConcept::setRegistryNumber
     * @covers \Ilios\CoreBundle\Entity\MeshConcept::getRegistryNumber
     */
    public function testSetRegistryNumber()
    {
        $this->basicSetTest('registryNumber', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshConcept::addTerm
     */
    public function testAddTerm()
    {
        $this->entityCollectionAddTest('term', 'MeshTerm', false, false, 'addConcept');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshConcept::removeTerm
     */
    public function testRemoveTerm()
    {
        $this->entityCollectionRemoveTest('term', 'MeshTerm', false, false, false, 'removeConcept');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshConcept::setTerms
     * @covers \Ilios\CoreBundle\Entity\MeshConcept::getTerms
     */
    public function testGetTerms()
    {
        $this->entityCollectionSetTest('term', 'MeshTerm', false, false, 'addConcept');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshConcept::addDescriptor
     */
    public function testAddDescriptor()
    {
        $this->entityCollectionAddTest('descriptor', 'MeshDescriptor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshConcept::removeDescriptor
     */
    public function testRemoveDescriptor()
    {
        $this->entityCollectionRemoveTest('descriptor', 'MeshDescriptor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshConcept::setDescriptors
     * @covers \Ilios\CoreBundle\Entity\MeshConcept::getDescriptors
     */
    public function testGetDescriptors()
    {
        $this->entityCollectionSetTest('descriptor', 'MeshDescriptor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshConcept::stampUpdate
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
