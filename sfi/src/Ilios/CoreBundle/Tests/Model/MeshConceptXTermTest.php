<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\MeshConceptXTerm;
use Mockery as m;

/**
 * Tests for Model MeshConceptXTerm
 */
class MeshConceptXTermTest extends ModelBase
{
    /**
     * @var MeshConceptXTerm
     */
    protected $object;

    /**
     * Instantiate a MeshConceptXTerm object
     */
    protected function setUp()
    {
        $this->object = new MeshConceptXTerm;
    }
    

    /**
     * @covers Ilios\CoreBundle\Model\MeshConceptXTerm::setMeshConceptUid
     */
    public function testSetMeshConceptUid()
    {
        $this->basicSetTest('meshConceptUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshConceptXTerm::getMeshConceptUid
     */
    public function testGetMeshConceptUid()
    {
        $this->basicGetTest('meshConceptUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshConceptXTerm::setMeshTermUid
     */
    public function testSetMeshTermUid()
    {
        $this->basicSetTest('meshTermUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshConceptXTerm::getMeshTermUid
     */
    public function testGetMeshTermUid()
    {
        $this->basicGetTest('meshTermUid', 'string');
    }
}
