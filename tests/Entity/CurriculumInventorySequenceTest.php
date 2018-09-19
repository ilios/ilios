<?php
namespace Tests\App\Entity;

use App\Entity\CurriculumInventorySequence;
use Mockery as m;

/**
 * Tests for Entity CurriculumInventorySequence
 */
class CurriculumInventorySequenceTest extends EntityBase
{
    /**
     * @var CurriculumInventorySequence
     */
    protected $object;

    /**
     * Instantiate a CurriculumInventorySequence object
     */
    protected function setUp()
    {
        $this->object = new CurriculumInventorySequence;
    }

    /**
     * @covers \App\Entity\CurriculumInventorySequence::setDescription
     * @covers \App\Entity\CurriculumInventorySequence::getDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers \App\Entity\CurriculumInventorySequence::setReport
     * @covers \App\Entity\CurriculumInventorySequence::getReport
     */
    public function testSetReport()
    {
        $this->entitySetTest('report', 'CurriculumInventoryReport');
    }
}
