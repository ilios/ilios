<?php
namespace Ilios\CoreBundle\Tests\Entity;


use Ilios\CoreBundle\Entity\CurriculumInventoryReport;
use Mockery as m;

/**
 * Tests for Entity CurriculumInventoryReport
 */
class CurriculumInventoryReportTest extends EntityBase
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
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryReport::getReportId
     */
    public function testGetReportId()
    {
        $this->basicGetTest('reportId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryReport::setYear
     */
    public function testSetYear()
    {
        $this->basicSetTest('year', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryReport::getYear
     */
    public function testGetYear()
    {
        $this->basicGetTest('year', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryReport::setName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryReport::getName
     */
    public function testGetName()
    {
        $this->basicGetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryReport::setDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryReport::getDescription
     */
    public function testGetDescription()
    {
        $this->basicGetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryReport::setStartDate
     */
    public function testSetStartDate()
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryReport::getStartDate
     */
    public function testGetStartDate()
    {
        $this->basicGetTest('startDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryReport::setEndDate
     */
    public function testSetEndDate()
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryReport::getEndDate
     */
    public function testGetEndDate()
    {
        $this->basicGetTest('endDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryReport::setExport
     */
    public function testSetExport()
    {
        $this->entitySetTest('export', 'CurriculumInventoryExport');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryReport::getExport
     */
    public function testGetExport()
    {
        $this->entityGetTest('export', 'CurriculumInventoryExport');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryReport::setSequence
     */
    public function testSetSequence()
    {
        $this->entitySetTest('sequence', 'CurriculumInventorySequence');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryReport::getSequence
     */
    public function testGetSequence()
    {
        $this->entityGetTest('sequence', 'CurriculumInventorySequence');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryReport::setProgram
     */
    public function testSetProgram()
    {
        $this->entitySetTest('program', 'Program');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryReport::getProgram
     */
    public function testGetProgram()
    {
        $this->entityGetTest('program', 'Program');
    }
}
