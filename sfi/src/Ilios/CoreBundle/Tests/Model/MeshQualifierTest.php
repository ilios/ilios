<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\MeshQualifier;
use Mockery as m;

/**
 * Tests for Model MeshQualifier
 */
class MeshQualifierTest extends BaseModel
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
     * @covers Ilios\CoreBundle\Model\MeshQualifier::setMeshQualifierUid
     */
    public function testSetMeshQualifierUid()
    {
        $this->basicSetTest('meshQualifierUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshQualifier::getMeshQualifierUid
     */
    public function testGetMeshQualifierUid()
    {
        $this->basicGetTest('meshQualifierUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshQualifier::setName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshQualifier::getName
     */
    public function testGetName()
    {
        $this->basicGetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshQualifier::setCreatedAt
     */
    public function testSetCreatedAt()
    {
        $this->basicSetTest('createdAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshQualifier::getCreatedAt
     */
    public function testGetCreatedAt()
    {
        $this->basicGetTest('createdAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshQualifier::setUpdatedAt
     */
    public function testSetUpdatedAt()
    {
        $this->basicSetTest('updatedAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshQualifier::getUpdatedAt
     */
    public function testGetUpdatedAt()
    {
        $this->basicGetTest('updatedAt', 'datetime');
    }
}
