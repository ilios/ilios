<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\CurriculumInventoryReport;
use App\Entity\Program;
use App\Entity\School;
use DateTime;

/**
 * Tests for Entity CurriculumInventoryReport
 * @group model
 */
class CurriculumInventoryReportTest extends EntityBase
{
    protected CurriculumInventoryReport $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new CurriculumInventoryReport();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'year',
            'startDate',
            'endDate',
        ];
        $this->validateNotBlanks($notBlank);

        $this->object->setYear(2001);
        $this->object->setStartDate(new DateTime());
        $this->object->setEndDate(new DateTime());
        $this->object->setName('');
        $this->object->setDescription('');
        $this->validate(0);
        $this->object->setName('test');
        $this->object->setDescription('test');
        $this->validate(0);
        $this->object->setName(null);
        $this->object->setDescription(null);
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::__construct
     */
    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getAcademicLevels());
        $this->assertCount(0, $this->object->getSequenceBlocks());
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::setYear
     * @covers \App\Entity\CurriculumInventoryReport::getYear
     */
    public function testSetYear(): void
    {
        $this->basicSetTest('year', 'integer');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::setName
     * @covers \App\Entity\CurriculumInventoryReport::getName
     */
    public function testSetName(): void
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::setDescription
     * @covers \App\Entity\CurriculumInventoryReport::getDescription
     */
    public function testSetDescription(): void
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::setStartDate
     * @covers \App\Entity\CurriculumInventoryReport::getStartDate
     */
    public function testSetStartDate(): void
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::setEndDate
     * @covers \App\Entity\CurriculumInventoryReport::getEndDate
     */
    public function testSetEndDate(): void
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::setExport
     * @covers \App\Entity\CurriculumInventoryReport::getExport
     */
    public function testSetExport(): void
    {
        $this->entitySetTest('export', 'CurriculumInventoryExport');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::setSequence
     * @covers \App\Entity\CurriculumInventoryReport::getSequence
     */
    public function testSetSequence(): void
    {
        $this->entitySetTest('sequence', 'CurriculumInventorySequence');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::setProgram
     * @covers \App\Entity\CurriculumInventoryReport::getProgram
     */
    public function testSetProgram(): void
    {
        $this->entitySetTest('program', 'Program');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::getSchool
     */
    public function testGetSchool(): void
    {
        $school = new School();
        $program = new Program();
        $program->setSchool($school);
        $report = new CurriculumInventoryReport();
        $report->setProgram($program);
        $this->assertEquals($school, $report->getSchool());

        $report = new CurriculumInventoryReport();
        $this->assertNull($report->getSchool());
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::addSequenceBlock
     */
    public function testAddSequenceBlock(): void
    {
        $this->entityCollectionAddTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::removeSequenceBlock
     */
    public function testRemoveSequenceBlock(): void
    {
        $this->entityCollectionRemoveTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::getSequenceBlocks
     */
    public function testGetSequenceBlocks(): void
    {
        $this->entityCollectionSetTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::addAcademicLevel
     */
    public function testAddAcademicLevel(): void
    {
        $this->entityCollectionAddTest('academicLevel', 'CurriculumInventoryAcademicLevel');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::removeAcademicLevel
     */
    public function testRemoveAcademicLevel(): void
    {
        $this->entityCollectionRemoveTest('academicLevel', 'CurriculumInventoryAcademicLevel');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::getAcademicLevels
     * @covers \App\Entity\CurriculumInventoryReport::setAcademicLevels
     */
    public function testGetAcademicLevels(): void
    {
        $this->entityCollectionSetTest('academicLevel', 'CurriculumInventoryAcademicLevel');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryReport::addAdministrator
     */
    public function testAddAdministrator(): void
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
    public function testRemoveAdministrator(): void
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
    public function testSetAdministrators(): void
    {
        $this->entityCollectionSetTest(
            'administrator',
            'User',
            false,
            false,
            'addAdministeredCurriculumInventoryReport'
        );
    }

    protected function getObject(): CurriculumInventoryReport
    {
        return $this->object;
    }
}
