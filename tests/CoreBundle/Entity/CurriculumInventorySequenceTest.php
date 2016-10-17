<?php
namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\CurriculumInventorySequence;
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
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequence::setDescription
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequence::getDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequence::setReport
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequence::getReport
     */
    public function testSetReport()
    {
        $this->entitySetTest('report', 'CurriculumInventoryReport');
    }
}
