<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\MeshConcept;
use Mockery as m;

/**
 * Tests for Model MeshConcept
 */
class MeshConceptTest extends ModelBase
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
     * @covers Ilios\CoreBundle\Model\MeshConcept::setMeshConceptUid
     */
    public function testSetMeshConceptUid()
    {
        $this->basicSetTest('meshConceptUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshConcept::getMeshConceptUid
     */
    public function testGetMeshConceptUid()
    {
        $this->basicGetTest('meshConceptUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshConcept::setName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshConcept::getName
     */
    public function testGetName()
    {
        $this->basicGetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshConcept::setUmlsUid
     */
    public function testSetUmlsUid()
    {
        $this->basicSetTest('umlsUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshConcept::getUmlsUid
     */
    public function testGetUmlsUid()
    {
        $this->basicGetTest('umlsUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshConcept::setPreferred
     */
    public function testSetPreferred()
    {
        $this->basicSetTest('preferred', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshConcept::getPreferred
     */
    public function testGetPreferred()
    {
        $this->basicGetTest('preferred', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshConcept::setScopeNote
     */
    public function testSetScopeNote()
    {
        $this->basicSetTest('scopeNote', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshConcept::getScopeNote
     */
    public function testGetScopeNote()
    {
        $this->basicGetTest('scopeNote', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshConcept::setCasn1Name
     */
    public function testSetCasn1Name()
    {
        $this->basicSetTest('casn1Name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshConcept::getCasn1Name
     */
    public function testGetCasn1Name()
    {
        $this->basicGetTest('casn1Name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshConcept::setRegistryNumber
     */
    public function testSetRegistryNumber()
    {
        $this->basicSetTest('registryNumber', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshConcept::getRegistryNumber
     */
    public function testGetRegistryNumber()
    {
        $this->basicGetTest('registryNumber', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshConcept::setCreatedAt
     */
    public function testSetCreatedAt()
    {
        $this->basicSetTest('createdAt', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshConcept::getCreatedAt
     */
    public function testGetCreatedAt()
    {
        $this->basicGetTest('createdAt', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshConcept::setUpdatedAt
     */
    public function testSetUpdatedAt()
    {
        $this->basicSetTest('updatedAt', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshConcept::getUpdatedAt
     */
    public function testGetUpdatedAt()
    {
        $this->basicGetTest('updatedAt', 'string');
    }
}
