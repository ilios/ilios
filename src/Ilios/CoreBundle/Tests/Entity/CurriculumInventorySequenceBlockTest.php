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
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::isRequired
     */
    public function testSetRequired()
    {
        $this->booleanSetTest('required');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setChildSequenceOrder
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::hasChildSequenceOrder
     */
    public function testSetChildSequenceOrder()
    {
        $this->booleanSetTest('childSequenceOrder', false);
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setOrderInSequence
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getOrderInSequence
     */
    public function testSetOrderInSequence()
    {
        $this->basicSetTest('orderInSequence', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setMinimum
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getMinimum
     */
    public function testSetMinimum()
    {
        $this->basicSetTest('minimum', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setMaximum
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getMaximum
     */
    public function testSetMaximum()
    {
        $this->basicSetTest('maximum', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setTrack
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::hasTrack
     */
    public function testSetTrack()
    {
        $this->booleanSetTest('track', false);
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setDescription
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setTitle
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setStartDate
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getStartDate
     */
    public function testSetStartDate()
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setEndDate
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getEndDate
     */
    public function testSetEndDate()
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setDuration
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getDuration
     */
    public function testSetDuration()
    {
        $this->basicSetTest('duration', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setAcademicLevel
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getAcademicLevel
     */
    public function testSetAcademicLevel()
    {
        $this->entitySetTest('academicLevel', 'CurriculumInventoryAcademicLevel');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setCourse
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getCourse
     */
    public function testSetCourse()
    {
        $this->entitySetTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setParent
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getParent
     */
    public function testSetParent()
    {
        $this->entitySetTest('parent', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setReport
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getReport
     */
    public function testSetReport()
    {
        $this->entitySetTest('report', 'CurriculumInventoryReport');
    }
}
