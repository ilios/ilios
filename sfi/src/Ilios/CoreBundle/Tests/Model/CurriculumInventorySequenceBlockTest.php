<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock;
use Mockery as m;

/**
 * Tests for Model CurriculumInventorySequenceBlock
 */
class CurriculumInventorySequenceBlockTest extends ModelBase
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
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock::getSequenceBlockId
     */
    public function testGetSequenceBlockId()
    {
        $this->basicGetTest('sequenceBlockId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock::setRequired
     */
    public function testSetRequired()
    {
        $this->basicSetTest('required', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock::getRequired
     */
    public function testGetRequired()
    {
        $this->basicGetTest('required', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock::setChildSequenceOrder
     */
    public function testSetChildSequenceOrder()
    {
        $this->basicSetTest('childSequenceOrder', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock::getChildSequenceOrder
     */
    public function testGetChildSequenceOrder()
    {
        $this->basicGetTest('childSequenceOrder', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock::setOrderInSequence
     */
    public function testSetOrderInSequence()
    {
        $this->basicSetTest('orderInSequence', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock::getOrderInSequence
     */
    public function testGetOrderInSequence()
    {
        $this->basicGetTest('orderInSequence', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock::setMinimum
     */
    public function testSetMinimum()
    {
        $this->basicSetTest('minimum', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock::getMinimum
     */
    public function testGetMinimum()
    {
        $this->basicGetTest('minimum', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock::setMaximum
     */
    public function testSetMaximum()
    {
        $this->basicSetTest('maximum', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock::getMaximum
     */
    public function testGetMaximum()
    {
        $this->basicGetTest('maximum', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock::setTrack
     */
    public function testSetTrack()
    {
        $this->basicSetTest('track', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock::getTrack
     */
    public function testGetTrack()
    {
        $this->basicGetTest('track', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock::setDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock::getDescription
     */
    public function testGetDescription()
    {
        $this->basicGetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock::getTitle
     */
    public function testGetTitle()
    {
        $this->basicGetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock::setStartDate
     */
    public function testSetStartDate()
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock::getStartDate
     */
    public function testGetStartDate()
    {
        $this->basicGetTest('startDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock::setEndDate
     */
    public function testSetEndDate()
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock::getEndDate
     */
    public function testGetEndDate()
    {
        $this->basicGetTest('endDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock::setDuration
     */
    public function testSetDuration()
    {
        $this->basicSetTest('duration', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock::getDuration
     */
    public function testGetDuration()
    {
        $this->basicGetTest('duration', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock::setAcademicLevel
     */
    public function testSetAcademicLevel()
    {
        $this->modelSetTest('academicLevel', 'CurriculumInventoryAcademicLevel');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock::getAcademicLevel
     */
    public function testGetAcademicLevel()
    {
        $this->modelGetTest('academicLevel', 'CurriculumInventoryAcademicLevel');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock::setCourse
     */
    public function testSetCourse()
    {
        $this->modelSetTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock::getCourse
     */
    public function testGetCourse()
    {
        $this->modelGetTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock::setParentSequenceBlock
     */
    public function testSetParentSequenceBlock()
    {
        $this->modelSetTest('parentSequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock::getParentSequenceBlock
     */
    public function testGetParentSequenceBlock()
    {
        $this->modelGetTest('parentSequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock::setReport
     */
    public function testSetReport()
    {
        $this->modelSetTest('report', 'CurriculumInventoryReport');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequenceBlock::getReport
     */
    public function testGetReport()
    {
        $this->modelGetTest('report', 'CurriculumInventoryReport');
    }
}
