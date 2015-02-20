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
     * @covers Ilios\CoreBundle\Entity\MeshQualifier::setName
     * @covers Ilios\CoreBundle\Entity\MeshQualifier::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshQualifier::setCreatedAt
     * @covers Ilios\CoreBundle\Entity\MeshQualifier::getCreatedAt
     */
    public function testSetCreatedAt()
    {
        $this->basicSetTest('createdAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshQualifier::setUpdatedAt
     * @covers Ilios\CoreBundle\Entity\MeshQualifier::getUpdatedAt
     */
    public function testSetUpdatedAt()
    {
        $this->basicSetTest('updatedAt', 'datetime');
    }
}
