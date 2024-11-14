<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Program;
use App\Entity\ProgramInterface;
use App\Entity\ProgramYear;
use App\Entity\School;
use Mockery as m;

/**
 * Tests for Entity ProgramYear
 * @group model
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Entity\ProgramYear::class)]
class ProgramYearTest extends EntityBase
{
    protected ProgramYear $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new ProgramYear();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'startYear',
        ];
        $this->object->setProgram(m::mock(ProgramInterface::class));

        $this->validateNotBlanks($notBlank);

        $this->object->setStartYear(3);
        $this->validate(0);
    }

    public function testNotNullValidation(): void
    {
        $notNull = [
            'program',
        ];
        $this->object->setStartYear(3);

        $this->validateNotNulls($notNull);
        $this->object->setProgram(m::mock(ProgramInterface::class));


        $this->validate(0);
    }

    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getCompetencies());
        $this->assertCount(0, $this->object->getDirectors());
        $this->assertCount(0, $this->object->getProgramYearObjectives());
        $this->assertCount(0, $this->object->getTerms());
    }

    public function testSetStartYear(): void
    {
        $this->basicSetTest('startYear', 'integer');
    }

    public function testSetLocked(): void
    {
        $this->booleanSetTest('locked');
    }

    public function testSetArchived(): void
    {
        $this->booleanSetTest('archived');
    }

    public function testSetProgram(): void
    {
        $this->entitySetTest('program', 'Program');
    }

    public function testAddDirector(): void
    {
        $this->entityCollectionAddTest('director', 'User');
    }

    public function testRemoveDirector(): void
    {
        $this->entityCollectionRemoveTest('director', 'User');
    }

    public function testGetDirectors(): void
    {
        $this->entityCollectionSetTest('director', 'User');
    }

    public function testGetSchool(): void
    {
        $school = new School();
        $program = new Program();
        $program->setSchool($school);
        $programYear = new ProgramYear();
        $programYear->setProgram($program);
        $this->assertEquals($school, $programYear->getSchool());
    }

    public function testAddTerm(): void
    {
        $this->entityCollectionAddTest('term', 'Term');
    }

    public function testRemoveTerm(): void
    {
        $this->entityCollectionRemoveTest('term', 'Term');
    }

    public function testSetTerms(): void
    {
        $this->entityCollectionSetTest('term', 'Term');
    }

    public function testAddProgramYearObjective(): void
    {
        $this->entityCollectionAddTest('programYearObjective', 'ProgramYearObjective');
    }

    public function testRemoveProgramYearObjective(): void
    {
        $this->entityCollectionRemoveTest('programYearObjective', 'ProgramYearObjective');
    }

    public function testGetProgramYearObjectives(): void
    {
        $this->entityCollectionSetTest('programYearObjective', 'ProgramYearObjective');
    }

    public function testAddCompetency(): void
    {
        $this->entityCollectionAddTest('competencies', 'Competency', 'getCompetencies', 'addCompetency');
    }

    public function testGetCompetencies(): void
    {
        $this->entityCollectionSetTest(
            'competencies',
            'Competency',
            'getCompetencies',
            'setCompetencies'
        );
    }

    public function testRemoveCompetency(): void
    {
        $this->entityCollectionRemoveTest(
            'competencies',
            'Competency',
            'getCompetencies',
            'addCompetency',
            'removeCompetency'
        );
    }

    public function testSetCohort(): void
    {
        $this->entitySetTest('cohort', 'Cohort');
    }

    protected function getObject(): ProgramYear
    {
        return $this->object;
    }
}
