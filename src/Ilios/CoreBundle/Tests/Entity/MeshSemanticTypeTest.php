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
     * @covers Ilios\CoreBundle\Entity\MeshSemanticType::setName
     * @covers Ilios\CoreBundle\Entity\MeshSemanticType::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshSemanticType::setCreatedAt
     * @covers Ilios\CoreBundle\Entity\MeshSemanticType::getCreatedAt
     */
    public function testSetCreatedAt()
    {
        $this->basicSetTest('createdAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshSemanticType::setUpdatedAt
     * @covers Ilios\CoreBundle\Entity\MeshSemanticType::getUpdatedAt
     */
    public function testSetUpdatedAt()
    {
        $this->basicSetTest('updatedAt', 'datetime');
    }
}
