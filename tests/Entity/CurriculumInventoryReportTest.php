<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\CurriculumInventoryReport;
use App\Entity\Program;
use App\Entity\School;
use DateTime;

/**
 * Tests for Entity CurriculumInventoryReport
 */
#[\PHPUnit\Framework\Attributes\Group('model')]
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Entity\CurriculumInventoryReport::class)]
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

    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getAcademicLevels());
        $this->assertCount(0, $this->object->getSequenceBlocks());
    }

    public function testSetYear(): void
    {
        $this->basicSetTest('year', 'integer');
    }

    public function testSetName(): void
    {
        $this->basicSetTest('name', 'string');
    }

    public function testSetDescription(): void
    {
        $this->basicSetTest('description', 'string');
    }

    public function testSetStartDate(): void
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    public function testSetEndDate(): void
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    public function testSetExport(): void
    {
        $this->entitySetTest('export', 'CurriculumInventoryExport');
    }

    public function testSetSequence(): void
    {
        $this->entitySetTest('sequence', 'CurriculumInventorySequence');
    }

    public function testSetProgram(): void
    {
        $this->entitySetTest('program', 'Program');
    }

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

    public function testAddSequenceBlock(): void
    {
        $this->entityCollectionAddTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    public function testRemoveSequenceBlock(): void
    {
        $this->entityCollectionRemoveTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    public function testGetSequenceBlocks(): void
    {
        $this->entityCollectionSetTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    public function testAddAcademicLevel(): void
    {
        $this->entityCollectionAddTest('academicLevel', 'CurriculumInventoryAcademicLevel');
    }

    public function testRemoveAcademicLevel(): void
    {
        $this->entityCollectionRemoveTest('academicLevel', 'CurriculumInventoryAcademicLevel');
    }

    public function testGetAcademicLevels(): void
    {
        $this->entityCollectionSetTest('academicLevel', 'CurriculumInventoryAcademicLevel');
    }

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
