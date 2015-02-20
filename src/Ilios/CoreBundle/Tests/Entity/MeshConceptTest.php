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
     * @covers Ilios\CoreBundle\Entity\MeshConcept::setName
     * @covers Ilios\CoreBundle\Entity\MeshConcept::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshConcept::setUmlsUid
     * @covers Ilios\CoreBundle\Entity\MeshConcept::getUmlsUid
     */
    public function testSetUmlsUid()
    {
        $this->basicSetTest('umlsUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshConcept::setPreferred
     * @covers Ilios\CoreBundle\Entity\MeshConcept::getPreferred
     */
    public function testSetPreferred()
    {
        $this->basicSetTest('preferred', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshConcept::setScopeNote
     * @covers Ilios\CoreBundle\Entity\MeshConcept::getScopeNote
     */
    public function testSetScopeNote()
    {
        $this->basicSetTest('scopeNote', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshConcept::setCasn1Name
     */
    public function testSetCasn1Name()
    {
        $this->basicSetTest('casn1Name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshConcept::setRegistryNumber
     * @covers Ilios\CoreBundle\Entity\MeshConcept::getRegistryNumber
     */
    public function testSetRegistryNumber()
    {
        $this->basicSetTest('registryNumber', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshConcept::setCreatedAt
     * @covers Ilios\CoreBundle\Entity\MeshConcept::getCreatedAt
     */
    public function testSetCreatedAt()
    {
        $this->basicSetTest('createdAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshConcept::setUpdatedAt
     * @covers Ilios\CoreBundle\Entity\MeshConcept::getUpdatedAt
     */
    public function testSetUpdatedAt()
    {
        $this->basicSetTest('updatedAt', 'datetime');
    }
}
