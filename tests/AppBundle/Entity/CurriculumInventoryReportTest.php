<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\CurriculumInventoryReport;
use AppBundle\Entity\Program;
use AppBundle\Entity\School;
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
     * @covers \AppBundle\Entity\CurriculumInventoryReport::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getAcademicLevels());
        $this->assertEmpty($this->object->getSequenceBlocks());
    }

    /**
     * @covers \AppBundle\Entity\CurriculumInventoryReport::setYear
     * @covers \AppBundle\Entity\CurriculumInventoryReport::getYear
     */
    public function testSetYear()
    {
        $this->basicSetTest('year', 'integer');
    }

    /**
     * @covers \AppBundle\Entity\CurriculumInventoryReport::setName
     * @covers \AppBundle\Entity\CurriculumInventoryReport::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \AppBundle\Entity\CurriculumInventoryReport::setDescription
     * @covers \AppBundle\Entity\CurriculumInventoryReport::getDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers \AppBundle\Entity\CurriculumInventoryReport::setStartDate
     * @covers \AppBundle\Entity\CurriculumInventoryReport::getStartDate
     */
    public function testSetStartDate()
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    /**
     * @covers \AppBundle\Entity\CurriculumInventoryReport::setEndDate
     * @covers \AppBundle\Entity\CurriculumInventoryReport::getEndDate
     */
    public function testSetEndDate()
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    /**
     * @covers \AppBundle\Entity\CurriculumInventoryReport::setExport
     * @covers \AppBundle\Entity\CurriculumInventoryReport::getExport
     */
    public function testSetExport()
    {
        $this->entitySetTest('export', 'CurriculumInventoryExport');
    }

    /**
     * @covers \AppBundle\Entity\CurriculumInventoryReport::setSequence
     * @covers \AppBundle\Entity\CurriculumInventoryReport::getSequence
     */
    public function testSetSequence()
    {
        $this->entitySetTest('sequence', 'CurriculumInventorySequence');
    }

    /**
     * @covers \AppBundle\Entity\CurriculumInventoryReport::setProgram
     * @covers \AppBundle\Entity\CurriculumInventoryReport::getProgram
     */
    public function testSetProgram()
    {
        $this->entitySetTest('program', 'Program');
    }

    /**
     * @covers \AppBundle\Entity\CurriculumInventoryReport::getSchool
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
     * @covers \AppBundle\Entity\CurriculumInventoryReport::addSequenceBlock
     */
    public function testAddSequenceBlock()
    {
        $this->entityCollectionAddTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \AppBundle\Entity\CurriculumInventoryReport::removeSequenceBlock
     */
    public function testRemoveSequenceBlock()
    {
        $this->entityCollectionRemoveTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \AppBundle\Entity\CurriculumInventoryReport::getSequenceBlocks
     */
    public function testGetSequenceBlocks()
    {
        $this->entityCollectionSetTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \AppBundle\Entity\CurriculumInventoryReport::addAcademicLevel
     */
    public function testAddAcademicLevel()
    {
        $this->entityCollectionAddTest('academicLevel', 'CurriculumInventoryAcademicLevel');
    }

    /**
     * @covers \AppBundle\Entity\CurriculumInventoryReport::removeAcademicLevel
     */
    public function testRemoveAcademicLevel()
    {
        $this->entityCollectionRemoveTest('academicLevel', 'CurriculumInventoryAcademicLevel');
    }

    /**
     * @covers \AppBundle\Entity\CurriculumInventoryReport::getAcademicLevels
     * @covers \AppBundle\Entity\CurriculumInventoryReport::setAcademicLevels
     */
    public function testGetAcademicLevels()
    {
        $this->entityCollectionSetTest('academicLevel', 'CurriculumInventoryAcademicLevel');
    }

    /**
     * @covers \AppBundle\Entity\CurriculumInventoryReport::addAdministrator
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
     * @covers \AppBundle\Entity\CurriculumInventoryReport::removeAdministrator
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
     * @covers \AppBundle\Entity\CurriculumInventoryReport::getAdministrators
     * @covers \AppBundle\Entity\CurriculumInventoryReport::setAdministrators
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
