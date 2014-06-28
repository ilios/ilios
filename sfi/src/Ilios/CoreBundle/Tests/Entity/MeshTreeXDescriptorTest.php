<?php
namespace Ilios\CoreBundle\Tests\Entity;


use Ilios\CoreBundle\Entity\MeshTreeXDescriptor;
use Mockery as m;

/**
 * Tests for Entity MeshTreeXDescriptor
 */
class MeshTreeXDescriptorTest extends EntityBase
{
    /**
     * @var MeshTreeXDescriptor
     */
    protected $object;

    /**
     * Instantiate a MeshTreeXDescriptor object
     */
    protected function setUp()
    {
        $this->object = new MeshTreeXDescriptor;
    }
    

    /**
     * @covers Ilios\CoreBundle\Entity\MeshTreeXDescriptor::setTreeNumber
     */
    public function testSetTreeNumber()
    {
        $this->basicSetTest('treeNumber', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshTreeXDescriptor::getTreeNumber
     */
    public function testGetTreeNumber()
    {
        $this->basicGetTest('treeNumber', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshTreeXDescriptor::setMeshDescriptorUid
     */
    public function testSetMeshDescriptorUid()
    {
        $this->basicSetTest('meshDescriptorUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshTreeXDescriptor::getMeshDescriptorUid
     */
    public function testGetMeshDescriptorUid()
    {
        $this->basicGetTest('meshDescriptorUid', 'string');
    }
}
