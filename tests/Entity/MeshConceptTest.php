<?php
namespace App\Tests\Entity;

use App\Entity\MeshConcept;
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
     * @covers \App\Entity\MeshConcept::__construct
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
     * @covers \App\Entity\MeshConcept::setName
     * @covers \App\Entity\MeshConcept::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \App\Entity\MeshConcept::setPreferred
     * @covers \App\Entity\MeshConcept::getPreferred
     */
    public function testSetPreferred()
    {
        $this->basicSetTest('preferred', 'string');
    }

    /**
     * @covers \App\Entity\MeshConcept::setScopeNote
     * @covers \App\Entity\MeshConcept::getScopeNote
     */
    public function testSetScopeNote()
    {
        $this->basicSetTest('scopeNote', 'string');
    }

    /**
     * @covers \App\Entity\MeshConcept::setCasn1Name
     */
    public function testSetCasn1Name()
    {
        $this->basicSetTest('casn1Name', 'string');
    }

    /**
     * @covers \App\Entity\MeshConcept::setRegistryNumber
     * @covers \App\Entity\MeshConcept::getRegistryNumber
     */
    public function testSetRegistryNumber()
    {
        $this->basicSetTest('registryNumber', 'string');
    }

    /**
     * @covers \App\Entity\MeshConcept::addTerm
     */
    public function testAddTerm()
    {
        $this->entityCollectionAddTest('term', 'MeshTerm', false, false, 'addConcept');
    }

    /**
     * @covers \App\Entity\MeshConcept::removeTerm
     */
    public function testRemoveTerm()
    {
        $this->entityCollectionRemoveTest('term', 'MeshTerm', false, false, false, 'removeConcept');
    }

    /**
     * @covers \App\Entity\MeshConcept::setTerms
     * @covers \App\Entity\MeshConcept::getTerms
     */
    public function testGetTerms()
    {
        $this->entityCollectionSetTest('term', 'MeshTerm', false, false, 'addConcept');
    }

    /**
     * @covers \App\Entity\MeshConcept::addDescriptor
     */
    public function testAddDescriptor()
    {
        $this->entityCollectionAddTest('descriptor', 'MeshDescriptor');
    }

    /**
     * @covers \App\Entity\MeshConcept::removeDescriptor
     */
    public function testRemoveDescriptor()
    {
        $this->entityCollectionRemoveTest('descriptor', 'MeshDescriptor');
    }

    /**
     * @covers \App\Entity\MeshConcept::setDescriptors
     * @covers \App\Entity\MeshConcept::getDescriptors
     */
    public function testGetDescriptors()
    {
        $this->entityCollectionSetTest('descriptor', 'MeshDescriptor');
    }

    /**
     * @covers \App\Entity\MeshConcept::stampUpdate
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
