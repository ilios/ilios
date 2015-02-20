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
     * @covers Ilios\CoreBundle\Entity\MeshPreviousIndexing::setPreviousIndexing
     * @covers Ilios\CoreBundle\Entity\MeshPreviousIndexing::getPreviousIndexing
     */
    public function testSetPreviousIndexing()
    {
        $this->basicSetTest('previousIndexing', 'string');
    }
}
