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
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryReport::setYear
     */
    public function testSetYear()
    {
        $this->basicSetTest('year', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryReport::setName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryReport::setDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryReport::setStartDate
     */
    public function testSetStartDate()
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryReport::setEndDate
     */
    public function testSetEndDate()
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryReport::setExport
     */
    public function testSetExport()
    {
        $this->entitySetTest('export', 'CurriculumInventoryExport');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryReport::setSequence
     */
    public function testSetSequence()
    {
        $this->entitySetTest('sequence', 'CurriculumInventorySequence');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryReport::setProgram
     */
    public function testSetProgram()
    {
        $this->entitySetTest('program', 'Program');
    }
}
