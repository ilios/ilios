<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\MeshUserSelection;
use Mockery as m;

/**
 * Tests for Model MeshUserSelection
 */
class MeshUserSelectionTest extends BaseModel
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
     * @covers Ilios\CoreBundle\Model\MeshUserSelection::getMeshUserSelectionId
     */
    public function testGetMeshUserSelectionId()
    {
        $this->basicGetTest('meshUserSelectionId', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshUserSelection::setMeshDescriptorUid
     */
    public function testSetMeshDescriptorUid()
    {
        $this->basicSetTest('meshDescriptorUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshUserSelection::getMeshDescriptorUid
     */
    public function testGetMeshDescriptorUid()
    {
        $this->basicGetTest('meshDescriptorUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshUserSelection::setSearchPhrase
     */
    public function testSetSearchPhrase()
    {
        $this->basicSetTest('searchPhrase', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshUserSelection::getSearchPhrase
     */
    public function testGetSearchPhrase()
    {
        $this->basicGetTest('searchPhrase', 'string');
    }
}
