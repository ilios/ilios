<?php
namespace Ilios\CoreBundle\Tests\Entity;


use Ilios\CoreBundle\Entity\MeshConceptXTerm;
use Mockery as m;

/**
 * Tests for Entity MeshConceptXTerm
 */
class MeshConceptXTermTest extends EntityBase
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
     * @covers Ilios\CoreBundle\Entity\MeshConceptXTerm::setMeshConceptUid
     */
    public function testSetMeshConceptUid()
    {
        $this->basicSetTest('meshConceptUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshConceptXTerm::getMeshConceptUid
     */
    public function testGetMeshConceptUid()
    {
        $this->basicGetTest('meshConceptUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshConceptXTerm::setMeshTermUid
     */
    public function testSetMeshTermUid()
    {
        $this->basicSetTest('meshTermUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshConceptXTerm::getMeshTermUid
     */
    public function testGetMeshTermUid()
    {
        $this->basicGetTest('meshTermUid', 'string');
    }
}
