<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\MeshSemanticType;
use Mockery as m;

/**
 * Tests for Model MeshSemanticType
 */
class MeshSemanticTypeTest extends ModelBase
{
    /**
     * @var MeshSemanticType
     */
    protected $object;

    /**
     * Instantiate a MeshSemanticType object
     */
    protected function setUp()
    {
        $this->object = new MeshSemanticType;
    }
    

    /**
     * @covers Ilios\CoreBundle\Model\MeshSemanticType::setMeshSemanticTypeUid
     */
    public function testSetMeshSemanticTypeUid()
    {
        $this->basicSetTest('meshSemanticTypeUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshSemanticType::getMeshSemanticTypeUid
     */
    public function testGetMeshSemanticTypeUid()
    {
        $this->basicGetTest('meshSemanticTypeUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshSemanticType::setName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshSemanticType::getName
     */
    public function testGetName()
    {
        $this->basicGetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshSemanticType::setCreatedAt
     */
    public function testSetCreatedAt()
    {
        $this->basicSetTest('createdAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshSemanticType::getCreatedAt
     */
    public function testGetCreatedAt()
    {
        $this->basicGetTest('createdAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshSemanticType::setUpdatedAt
     */
    public function testSetUpdatedAt()
    {
        $this->basicSetTest('updatedAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshSemanticType::getUpdatedAt
     */
    public function testGetUpdatedAt()
    {
        $this->basicGetTest('updatedAt', 'datetime');
    }
}
