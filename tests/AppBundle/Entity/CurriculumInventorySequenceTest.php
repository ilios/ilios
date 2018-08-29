<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\CurriculumInventorySequence;
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
     * @covers \AppBundle\Entity\CurriculumInventorySequence::setDescription
     * @covers \AppBundle\Entity\CurriculumInventorySequence::getDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers \AppBundle\Entity\CurriculumInventorySequence::setReport
     * @covers \AppBundle\Entity\CurriculumInventorySequence::getReport
     */
    public function testSetReport()
    {
        $this->entitySetTest('report', 'CurriculumInventoryReport');
    }
}
