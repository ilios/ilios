<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\MeshPreviousIndexing;
use Mockery as m;

/**
 * Tests for Model MeshPreviousIndexing
 */
class MeshPreviousIndexingTest extends ModelBase
{
    /**
     * @var MeshPreviousIndexing
     */
    protected $object;

    /**
     * Instantiate a MeshPreviousIndexing object
     */
    protected function setUp()
    {
        $this->object = new MeshPreviousIndexing;
    }
    

    /**
     * @covers Ilios\CoreBundle\Model\MeshPreviousIndexing::setMeshDescriptorUid
     */
    public function testSetMeshDescriptorUid()
    {
        $this->basicSetTest('meshDescriptorUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshPreviousIndexing::getMeshDescriptorUid
     */
    public function testGetMeshDescriptorUid()
    {
        $this->basicGetTest('meshDescriptorUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshPreviousIndexing::setPreviousIndexing
     */
    public function testSetPreviousIndexing()
    {
        $this->basicSetTest('previousIndexing', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshPreviousIndexing::getPreviousIndexing
     */
    public function testGetPreviousIndexing()
    {
        $this->basicGetTest('previousIndexing', 'string');
    }
}
