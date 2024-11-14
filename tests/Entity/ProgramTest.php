<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Program;
use App\Entity\SchoolInterface;
use Mockery as m;

/**
 * Tests for Entity Program
 * @group model
 */
class ProgramTest extends EntityBase
{
    protected Program $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new Program();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'title',
            'duration',
        ];
        $this->object->setSchool(m::mock(SchoolInterface::class));

        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('DVc');
        $this->object->setDuration(30);
        $this->object->setShortTitle('');
        $this->validate(0);
        $this->object->setShortTitle('test');
        $this->validate(0);
    }

    public function testNotNullValidation(): void
    {
        $notNull = [
            'school',
        ];
        $this->object->setTitle('DVc');
        $this->object->setDuration(30);

        $this->validateNotNulls($notNull);

        $this->object->setSchool(m::mock(SchoolInterface::class));

        $this->validate(0);
    }

    /**
     * @covers \App\Entity\Program::__construct
     */
    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getProgramYears());
        $this->assertCount(0, $this->object->getCurriculumInventoryReports());
        $this->assertCount(0, $this->object->getDirectors());
    }

    /**
     * @covers \App\Entity\Program::setTitle
     * @covers \App\Entity\Program::getTitle
     */
    public function testSetTitle(): void
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\Program::setShortTitle
     * @covers \App\Entity\Program::getShortTitle
     */
    public function testSetShortTitle(): void
    {
        $this->basicSetTest('shortTitle', 'string');
    }

    /**
     * @covers \App\Entity\Program::setDuration
     * @covers \App\Entity\Program::getDuration
     */
    public function testSetDuration(): void
    {
        $this->basicSetTest('duration', 'integer');
    }

    /**
     * @covers \App\Entity\Program::setSchool
     * @covers \App\Entity\Program::getSchool
     */
    public function testSetSchool(): void
    {
        $this->entitySetTest('school', 'School');
    }

    /**
     * @covers \App\Entity\Program::addProgramYear
     */
    public function testAddProgramYear(): void
    {
        $this->entityCollectionAddTest('programYear', 'ProgramYear');
    }

    /**
     * @covers \App\Entity\Program::removeProgramYear
     */
    public function testRemoveProgramYear(): void
    {
        $this->entityCollectionRemoveTest('programYear', 'ProgramYear');
    }

    /**
     * @covers \App\Entity\Program::getProgramYears
     */
    public function testGetProgramYears(): void
    {
        $this->entityCollectionSetTest('programYear', 'ProgramYear');
    }

    /**
     * @covers \App\Entity\Program::addCurriculumInventoryReport
     */
    public function testAddCurriculumInventoryReport(): void
    {
        $this->entityCollectionAddTest('curriculumInventoryReport', 'CurriculumInventoryReport');
    }

    /**
     * @covers \App\Entity\Program::removeCurriculumInventoryReport
     */
    public function testRemoveCurriculumInventoryReport(): void
    {
        $this->entityCollectionRemoveTest('curriculumInventoryReport', 'CurriculumInventoryReport');
    }

    /**
     * @covers \App\Entity\Program::getCurriculumInventoryReports
     * @covers \App\Entity\Program::setCurriculumInventoryReports
     */
    public function testGetCurriculumInventoryReports(): void
    {
        $this->entityCollectionSetTest('curriculumInventoryReport', 'CurriculumInventoryReport');
    }

    /**
     * @covers \App\Entity\Program::addDirector
     */
    public function testAddDirector(): void
    {
        $this->entityCollectionAddTest('director', 'User', false, false, 'addDirectedProgram');
    }

    /**
     * @covers \App\Entity\Program::removeDirector
     */
    public function testRemoveDirector(): void
    {
        $this->entityCollectionRemoveTest('director', 'User', false, false, false, 'removeDirectedProgram');
    }

    /**
     * @covers \App\Entity\Program::getDirectors
     */
    public function testGetDirectors(): void
    {
        $this->entityCollectionSetTest('director', 'User', false, false, 'addDirectedProgram');
    }

    protected function getObject(): Program
    {
        return $this->object;
    }
}
