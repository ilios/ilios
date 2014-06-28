<?php
namespace Ilios\CoreBundle\Tests\Entity;


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
    

    /**
     * @covers Ilios\CoreBundle\Entity\MeshConcept::setMeshConceptUid
     */
    public function testSetMeshConceptUid()
    {
        $this->basicSetTest('meshConceptUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshConcept::getMeshConceptUid
     */
    public function testGetMeshConceptUid()
    {
        $this->basicGetTest('meshConceptUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshConcept::setName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshConcept::getName
     */
    public function testGetName()
    {
        $this->basicGetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshConcept::setUmlsUid
     */
    public function testSetUmlsUid()
    {
        $this->basicSetTest('umlsUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshConcept::getUmlsUid
     */
    public function testGetUmlsUid()
    {
        $this->basicGetTest('umlsUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshConcept::setPreferred
     */
    public function testSetPreferred()
    {
        $this->basicSetTest('preferred', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshConcept::getPreferred
     */
    public function testGetPreferred()
    {
        $this->basicGetTest('preferred', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshConcept::setScopeNote
     */
    public function testSetScopeNote()
    {
        $this->basicSetTest('scopeNote', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshConcept::getScopeNote
     */
    public function testGetScopeNote()
    {
        $this->basicGetTest('scopeNote', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshConcept::setCasn1Name
     */
    public function testSetCasn1Name()
    {
        $this->basicSetTest('casn1Name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshConcept::getCasn1Name
     */
    public function testGetCasn1Name()
    {
        $this->basicGetTest('casn1Name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshConcept::setRegistryNumber
     */
    public function testSetRegistryNumber()
    {
        $this->basicSetTest('registryNumber', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshConcept::getRegistryNumber
     */
    public function testGetRegistryNumber()
    {
        $this->basicGetTest('registryNumber', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshConcept::setCreatedAt
     */
    public function testSetCreatedAt()
    {
        $this->basicSetTest('createdAt', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshConcept::getCreatedAt
     */
    public function testGetCreatedAt()
    {
        $this->basicGetTest('createdAt', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshConcept::setUpdatedAt
     */
    public function testSetUpdatedAt()
    {
        $this->basicSetTest('updatedAt', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshConcept::getUpdatedAt
     */
    public function testGetUpdatedAt()
    {
        $this->basicGetTest('updatedAt', 'string');
    }
}
