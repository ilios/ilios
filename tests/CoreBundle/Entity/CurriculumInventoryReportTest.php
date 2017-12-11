<?php
namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\CurriculumInventoryReport;
use Ilios\CoreBundle\Entity\Program;
use Ilios\CoreBundle\Entity\School;
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
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryReport::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getAcademicLevels());
        $this->assertEmpty($this->object->getSequenceBlocks());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryReport::setYear
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryReport::getYear
     */
    public function testSetYear()
    {
        $this->basicSetTest('year', 'integer');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryReport::setName
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryReport::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryReport::setDescription
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryReport::getDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryReport::setStartDate
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryReport::getStartDate
     */
    public function testSetStartDate()
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryReport::setEndDate
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryReport::getEndDate
     */
    public function testSetEndDate()
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryReport::setExport
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryReport::getExport
     */
    public function testSetExport()
    {
        $this->entitySetTest('export', 'CurriculumInventoryExport');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryReport::setSequence
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryReport::getSequence
     */
    public function testSetSequence()
    {
        $this->entitySetTest('sequence', 'CurriculumInventorySequence');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryReport::setProgram
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryReport::getProgram
     */
    public function testSetProgram()
    {
        $this->entitySetTest('program', 'Program');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryReport::getSchool
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
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryReport::addSequenceBlock
     */
    public function testAddSequenceBlock()
    {
        $this->entityCollectionAddTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryReport::removeSequenceBlock
     */
    public function testRemoveSequenceBlock()
    {
        $this->entityCollectionRemoveTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryReport::getSequenceBlocks
     */
    public function testGetSequenceBlocks()
    {
        $this->entityCollectionSetTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryReport::addAcademicLevel
     */
    public function testAddAcademicLevel()
    {
        $this->entityCollectionAddTest('academicLevel', 'CurriculumInventoryAcademicLevel');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryReport::removeAcademicLevel
     */
    public function testRemoveAcademicLevel()
    {
        $this->entityCollectionRemoveTest('academicLevel', 'CurriculumInventoryAcademicLevel');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryReport::getAcademicLevels
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryReport::setAcademicLevels
     */
    public function testGetAcademicLevels()
    {
        $this->entityCollectionSetTest('academicLevel', 'CurriculumInventoryAcademicLevel');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryReport::addAdministrator
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
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryReport::removeAdministrator
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
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryReport::getAdministrators
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryReport::setAdministrators
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
