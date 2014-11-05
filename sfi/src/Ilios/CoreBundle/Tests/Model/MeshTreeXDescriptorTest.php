<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\MeshTreeXDescriptor;
use Mockery as m;

/**
 * Tests for Model MeshTreeXDescriptor
 */
class MeshTreeXDescriptorTest extends BaseModel
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
     * @covers Ilios\CoreBundle\Model\MeshTreeXDescriptor::setTreeNumber
     */
    public function testSetTreeNumber()
    {
        $this->basicSetTest('treeNumber', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshTreeXDescriptor::getTreeNumber
     */
    public function testGetTreeNumber()
    {
        $this->basicGetTest('treeNumber', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshTreeXDescriptor::setMeshDescriptorUid
     */
    public function testSetMeshDescriptorUid()
    {
        $this->basicSetTest('meshDescriptorUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshTreeXDescriptor::getMeshDescriptorUid
     */
    public function testGetMeshDescriptorUid()
    {
        $this->basicGetTest('meshDescriptorUid', 'string');
    }
}
