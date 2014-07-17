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
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getSequenceBlockId
     */
    public function testGetSequenceBlockId()
    {
        $this->basicGetTest('sequenceBlockId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setRequired
     */
    public function testSetRequired()
    {
        $this->basicSetTest('required', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getRequired
     */
    public function testGetRequired()
    {
        $this->basicGetTest('required', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setChildSequenceOrder
     */
    public function testSetChildSequenceOrder()
    {
        $this->basicSetTest('childSequenceOrder', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getChildSequenceOrder
     */
    public function testGetChildSequenceOrder()
    {
        $this->basicGetTest('childSequenceOrder', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setOrderInSequence
     */
    public function testSetOrderInSequence()
    {
        $this->basicSetTest('orderInSequence', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getOrderInSequence
     */
    public function testGetOrderInSequence()
    {
        $this->basicGetTest('orderInSequence', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setMinimum
     */
    public function testSetMinimum()
    {
        $this->basicSetTest('minimum', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getMinimum
     */
    public function testGetMinimum()
    {
        $this->basicGetTest('minimum', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setMaximum
     */
    public function testSetMaximum()
    {
        $this->basicSetTest('maximum', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getMaximum
     */
    public function testGetMaximum()
    {
        $this->basicGetTest('maximum', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setTrack
     */
    public function testSetTrack()
    {
        $this->basicSetTest('track', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getTrack
     */
    public function testGetTrack()
    {
        $this->basicGetTest('track', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getDescription
     */
    public function testGetDescription()
    {
        $this->basicGetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getTitle
     */
    public function testGetTitle()
    {
        $this->basicGetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setStartDate
     */
    public function testSetStartDate()
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getStartDate
     */
    public function testGetStartDate()
    {
        $this->basicGetTest('startDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setEndDate
     */
    public function testSetEndDate()
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getEndDate
     */
    public function testGetEndDate()
    {
        $this->basicGetTest('endDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setDuration
     */
    public function testSetDuration()
    {
        $this->basicSetTest('duration', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getDuration
     */
    public function testGetDuration()
    {
        $this->basicGetTest('duration', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setAcademicLevel
     */
    public function testSetAcademicLevel()
    {
        $this->entitySetTest('academicLevel', 'CurriculumInventoryAcademicLevel');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getAcademicLevel
     */
    public function testGetAcademicLevel()
    {
        $this->entityGetTest('academicLevel', 'CurriculumInventoryAcademicLevel');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setCourse
     */
    public function testSetCourse()
    {
        $this->entitySetTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getCourse
     */
    public function testGetCourse()
    {
        $this->entityGetTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setParentSequenceBlock
     */
    public function testSetParentSequenceBlock()
    {
        $this->entitySetTest('parentSequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getParentSequenceBlock
     */
    public function testGetParentSequenceBlock()
    {
        $this->entityGetTest('parentSequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setReport
     */
    public function testSetReport()
    {
        $this->entitySetTest('report', 'CurriculumInventoryReport');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getReport
     */
    public function testGetReport()
    {
        $this->entityGetTest('report', 'CurriculumInventoryReport');
    }
}
