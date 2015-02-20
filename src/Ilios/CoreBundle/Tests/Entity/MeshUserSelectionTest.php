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
     * @covers Ilios\CoreBundle\Entity\MeshUserSelection::setMeshDescriptor
     */
    public function testSetMeshDescriptor()
    {
        $this->entitySetTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshUserSelection::setSearchPhrase
     */
    public function testSetSearchPhrase()
    {
        $this->basicSetTest('searchPhrase', 'string');
    }
}
