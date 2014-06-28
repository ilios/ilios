<?php
namespace Ilios\CoreBundle\Tests\Entity;


use Ilios\CoreBundle\Entity\MeshPreviousIndexing;
use Mockery as m;

/**
 * Tests for Entity MeshPreviousIndexing
 */
class MeshPreviousIndexingTest extends EntityBase
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
     * @covers Ilios\CoreBundle\Entity\MeshPreviousIndexing::setMeshDescriptorUid
     */
    public function testSetMeshDescriptorUid()
    {
        $this->basicSetTest('meshDescriptorUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshPreviousIndexing::getMeshDescriptorUid
     */
    public function testGetMeshDescriptorUid()
    {
        $this->basicGetTest('meshDescriptorUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshPreviousIndexing::setPreviousIndexing
     */
    public function testSetPreviousIndexing()
    {
        $this->basicSetTest('previousIndexing', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshPreviousIndexing::getPreviousIndexing
     */
    public function testGetPreviousIndexing()
    {
        $this->basicGetTest('previousIndexing', 'string');
    }
}
