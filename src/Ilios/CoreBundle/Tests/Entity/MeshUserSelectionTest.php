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

    // NotBlank() tests are not necessary - no NotBlank() fields

    /**
     * @covers Ilios\CoreBundle\Entity\MeshUserSelection::setMeshDescriptor
     * @covers Ilios\CoreBundle\Entity\MeshUserSelection::getMeshDescriptor
     */
    public function testSetMeshDescriptor()
    {
        $this->entitySetTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshUserSelection::setSearchPhrase
     * @covers Ilios\CoreBundle\Entity\MeshUserSelection::getSearchPhrase
     */
    public function testSetSearchPhrase()
    {
        $this->basicSetTest('searchPhrase', 'string');
    }
}
