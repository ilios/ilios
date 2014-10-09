<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\CurriculumInventoryReport;
use Mockery as m;

/**
 * Tests for Model CurriculumInventoryReport
 */
class CurriculumInventoryReportTest extends ModelBase
{
    /**
     * @var CurriculumInventoryReport
     */
    protected $object;

    /**
     * Instantiate a CurriculumInventoryReport object
     */
    protected function setUp()
    {
        $this->object = new CurriculumInventoryReport;
    }
    

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryReport::getReportId
     */
    public function testGetReportId()
    {
        $this->basicGetTest('reportId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryReport::setYear
     */
    public function testSetYear()
    {
        $this->basicSetTest('year', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryReport::getYear
     */
    public function testGetYear()
    {
        $this->basicGetTest('year', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryReport::setName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryReport::getName
     */
    public function testGetName()
    {
        $this->basicGetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryReport::setDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryReport::getDescription
     */
    public function testGetDescription()
    {
        $this->basicGetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryReport::setStartDate
     */
    public function testSetStartDate()
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryReport::getStartDate
     */
    public function testGetStartDate()
    {
        $this->basicGetTest('startDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryReport::setEndDate
     */
    public function testSetEndDate()
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryReport::getEndDate
     */
    public function testGetEndDate()
    {
        $this->basicGetTest('endDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryReport::setExport
     */
    public function testSetExport()
    {
        $this->modelSetTest('export', 'CurriculumInventoryExport');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryReport::getExport
     */
    public function testGetExport()
    {
        $this->modelGetTest('export', 'CurriculumInventoryExport');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryReport::setSequence
     */
    public function testSetSequence()
    {
        $this->modelSetTest('sequence', 'CurriculumInventorySequence');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryReport::getSequence
     */
    public function testGetSequence()
    {
        $this->modelGetTest('sequence', 'CurriculumInventorySequence');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryReport::setProgram
     */
    public function testSetProgram()
    {
        $this->modelSetTest('program', 'Program');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryReport::getProgram
     */
    public function testGetProgram()
    {
        $this->modelGetTest('program', 'Program');
    }
}
