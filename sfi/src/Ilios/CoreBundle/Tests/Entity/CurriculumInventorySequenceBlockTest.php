<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock;
use Mockery as m;

/**
 * Tests for Entity CurriculumInventorySequenceBlock
 */
class CurriculumInventorySequenceBlockTest extends EntityBase
{
    /**
     * @var CurriculumInventorySequenceBlock
     */
    protected $object;

    /**
     * Instantiate a CurriculumInventorySequenceBlock object
     */
    protected function setUp()
    {
        $this->object = new CurriculumInventorySequenceBlock;
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setRequired
     */
    public function testSetRequired()
    {
        $this->booleanSetTest('required');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setChildSequenceOrder
     */
    public function testSetChildSequenceOrder()
    {
        $this->booleanSetTest('childSequenceOrder', false);
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setOrderInSequence
     */
    public function testSetOrderInSequence()
    {
        $this->basicSetTest('orderInSequence', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setMinimum
     */
    public function testSetMinimum()
    {
        $this->basicSetTest('minimum', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setMaximum
     */
    public function testSetMaximum()
    {
        $this->basicSetTest('maximum', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setTrack
     */
    public function testSetTrack()
    {
        $this->booleanSetTest('track', false);
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setStartDate
     */
    public function testSetStartDate()
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setEndDate
     */
    public function testSetEndDate()
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setDuration
     */
    public function testSetDuration()
    {
        $this->basicSetTest('duration', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setAcademicLevel
     */
    public function testSetAcademicLevel()
    {
        $this->entitySetTest('academicLevel', 'CurriculumInventoryAcademicLevel');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setCourse
     */
    public function testSetCourse()
    {
        $this->entitySetTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setParentSequenceBlock
     */
    public function testSetParent()
    {
        $this->entitySetTest('parent', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setReport
     */
    public function testSetReport()
    {
        $this->entitySetTest('report', 'CurriculumInventoryReport');
    }
}
