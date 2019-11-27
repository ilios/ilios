<?php
namespace App\Tests\Entity;

use App\Entity\CurriculumInventoryReport;
use App\Entity\Program;
use App\Entity\School;
use Mockery as m;

/**
 * Tests for Entity CurriculumInventoryReport
 * @group model
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

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'year',
            'startDate',
            'endDate'
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setYear(2001);
        $this->object->setStartDate(new \DateTime());
        $this->object->setEndDate(new \DateTime());
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getAcademicLevels());
        $this->assertEmpty($this->object->getSequenceBlocks());
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::setYear
     * @covers \App\Entity\CurriculumInventoryReport::getYear
     */
    public function testSetYear()
    {
        $this->basicSetTest('year', 'integer');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::setName
     * @covers \App\Entity\CurriculumInventoryReport::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::setDescription
     * @covers \App\Entity\CurriculumInventoryReport::getDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::setStartDate
     * @covers \App\Entity\CurriculumInventoryReport::getStartDate
     */
    public function testSetStartDate()
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::setEndDate
     * @covers \App\Entity\CurriculumInventoryReport::getEndDate
     */
    public function testSetEndDate()
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::setExport
     * @covers \App\Entity\CurriculumInventoryReport::getExport
     */
    public function testSetExport()
    {
        $this->entitySetTest('export', 'CurriculumInventoryExport');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::setSequence
     * @covers \App\Entity\CurriculumInventoryReport::getSequence
     */
    public function testSetSequence()
    {
        $this->entitySetTest('sequence', 'CurriculumInventorySequence');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::setProgram
     * @covers \App\Entity\CurriculumInventoryReport::getProgram
     */
    public function testSetProgram()
    {
        $this->entitySetTest('program', 'Program');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::getSchool
     */
    public function testGetSchool()
    {
        $school = new School();
        $program = new Program();
        $program->setSchool($school);
        $report = new CurriculumInventoryReport();
        $report->setProgram($program);
        $this->assertEquals($school, $report->getSchool());

        $program = new Program();
        $report = new CurriculumInventoryReport();
        $report->setProgram($program);
        $this->assertNull($report->getSchool());

        $report = new CurriculumInventoryReport();
        $this->assertNull($report->getSchool());
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::addSequenceBlock
     */
    public function testAddSequenceBlock()
    {
        $this->entityCollectionAddTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::removeSequenceBlock
     */
    public function testRemoveSequenceBlock()
    {
        $this->entityCollectionRemoveTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::getSequenceBlocks
     */
    public function testGetSequenceBlocks()
    {
        $this->entityCollectionSetTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::addAcademicLevel
     */
    public function testAddAcademicLevel()
    {
        $this->entityCollectionAddTest('academicLevel', 'CurriculumInventoryAcademicLevel');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::removeAcademicLevel
     */
    public function testRemoveAcademicLevel()
    {
        $this->entityCollectionRemoveTest('academicLevel', 'CurriculumInventoryAcademicLevel');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::getAcademicLevels
     * @covers \App\Entity\CurriculumInventoryReport::setAcademicLevels
     */
    public function testGetAcademicLevels()
    {
        $this->entityCollectionSetTest('academicLevel', 'CurriculumInventoryAcademicLevel');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::addAdministrator
     */
    public function testAddAdministrator()
    {
        $this->entityCollectionAddTest(
            'administrator',
            'User',
            false,
            false,
            'addAdministeredCurriculumInventoryReport'
        );
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::removeAdministrator
     */
    public function testRemoveAdministrator()
    {
        $this->entityCollectionRemoveTest(
            'administrator',
            'User',
            false,
            false,
            false,
            'removeAdministeredCurriculumInventoryReport'
        );
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::getAdministrators
     * @covers \App\Entity\CurriculumInventoryReport::setAdministrators
     */
    public function testSetAdministrators()
    {
        $this->entityCollectionSetTest(
            'administrator',
            'User',
            false,
            false,
            'addAdministeredCurriculumInventoryReport'
        );
    }
}
