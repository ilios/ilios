<?php
namespace Ilios\CoreBundle\Tests\Entity;


use Ilios\CoreBundle\Entity\MeshSemanticType;
use Mockery as m;

/**
 * Tests for Entity MeshSemanticType
 */
class MeshSemanticTypeTest extends EntityBase
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
     * @covers Ilios\CoreBundle\Entity\MeshSemanticType::setMeshSemanticTypeUid
     */
    public function testSetMeshSemanticTypeUid()
    {
        $this->basicSetTest('meshSemanticTypeUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshSemanticType::getMeshSemanticTypeUid
     */
    public function testGetMeshSemanticTypeUid()
    {
        $this->basicGetTest('meshSemanticTypeUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshSemanticType::setName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshSemanticType::getName
     */
    public function testGetName()
    {
        $this->basicGetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshSemanticType::setCreatedAt
     */
    public function testSetCreatedAt()
    {
        $this->basicSetTest('createdAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshSemanticType::getCreatedAt
     */
    public function testGetCreatedAt()
    {
        $this->basicGetTest('createdAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshSemanticType::setUpdatedAt
     */
    public function testSetUpdatedAt()
    {
        $this->basicSetTest('updatedAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshSemanticType::getUpdatedAt
     */
    public function testGetUpdatedAt()
    {
        $this->basicGetTest('updatedAt', 'datetime');
    }
}
