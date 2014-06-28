<?php
namespace Ilios\CoreBundle\Tests\Entity;


use Ilios\CoreBundle\Entity\MeshQualifier;
use Mockery as m;

/**
 * Tests for Entity MeshQualifier
 */
class MeshQualifierTest extends EntityBase
{
    /**
     * @var MeshQualifier
     */
    protected $object;

    /**
     * Instantiate a MeshQualifier object
     */
    protected function setUp()
    {
        $this->object = new MeshQualifier;
    }
    

    /**
     * @covers Ilios\CoreBundle\Entity\MeshQualifier::setMeshQualifierUid
     */
    public function testSetMeshQualifierUid()
    {
        $this->basicSetTest('meshQualifierUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshQualifier::getMeshQualifierUid
     */
    public function testGetMeshQualifierUid()
    {
        $this->basicGetTest('meshQualifierUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshQualifier::setName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshQualifier::getName
     */
    public function testGetName()
    {
        $this->basicGetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshQualifier::setCreatedAt
     */
    public function testSetCreatedAt()
    {
        $this->basicSetTest('createdAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshQualifier::getCreatedAt
     */
    public function testGetCreatedAt()
    {
        $this->basicGetTest('createdAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshQualifier::setUpdatedAt
     */
    public function testSetUpdatedAt()
    {
        $this->basicSetTest('updatedAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshQualifier::getUpdatedAt
     */
    public function testGetUpdatedAt()
    {
        $this->basicGetTest('updatedAt', 'datetime');
    }
}
