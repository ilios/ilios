<?php
namespace Ilios\CoreBundle\Tests\Entity;


use Ilios\CoreBundle\Entity\MeshUserSelection;
use Mockery as m;

/**
 * Tests for Entity MeshUserSelection
 */
class MeshUserSelectionTest extends EntityBase
{
    /**
     * @var MeshUserSelection
     */
    protected $object;

    /**
     * Instantiate a MeshUserSelection object
     */
    protected function setUp()
    {
        $this->object = new MeshUserSelection;
    }
    

    /**
     * @covers Ilios\CoreBundle\Entity\MeshUserSelection::getMeshUserSelectionId
     */
    public function testGetMeshUserSelectionId()
    {
        $this->basicGetTest('meshUserSelectionId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshUserSelection::setMeshDescriptorUid
     */
    public function testSetMeshDescriptorUid()
    {
        $this->basicSetTest('meshDescriptorUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshUserSelection::getMeshDescriptorUid
     */
    public function testGetMeshDescriptorUid()
    {
        $this->basicGetTest('meshDescriptorUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshUserSelection::setSearchPhrase
     */
    public function testSetSearchPhrase()
    {
        $this->basicSetTest('searchPhrase', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshUserSelection::getSearchPhrase
     */
    public function testGetSearchPhrase()
    {
        $this->basicGetTest('searchPhrase', 'string');
    }
}
