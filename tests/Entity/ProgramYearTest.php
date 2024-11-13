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

    /**
     * @covers \App\Entity\ProgramYear::__construct
     */
    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getCompetencies());
        $this->assertCount(0, $this->object->getDirectors());
        $this->assertCount(0, $this->object->getProgramYearObjectives());
        $this->assertCount(0, $this->object->getTerms());
    }

    /**
     * @covers \App\Entity\ProgramYear::setStartYear
     * @covers \App\Entity\ProgramYear::getStartYear
     */
    public function testSetStartYear(): void
    {
        $this->basicSetTest('startYear', 'integer');
    }

    /**
     * @covers \App\Entity\ProgramYear::setLocked
     * @covers \App\Entity\ProgramYear::isLocked
     */
    public function testSetLocked(): void
    {
        $this->booleanSetTest('locked');
    }

    /**
     * @covers \App\Entity\ProgramYear::setArchived
     * @covers \App\Entity\ProgramYear::isArchived
     */
    public function testSetArchived(): void
    {
        $this->booleanSetTest('archived');
    }

   /**
     * @covers \App\Entity\ProgramYear::setProgram
     * @covers \App\Entity\ProgramYear::getProgram
     */
    public function testSetProgram(): void
    {
        $this->entitySetTest('program', 'Program');
    }

    /**
     * @covers \App\Entity\ProgramYear::addDirector
     */
    public function testAddDirector(): void
    {
        $this->entityCollectionAddTest('director', 'User');
    }

    /**
     * @covers \App\Entity\ProgramYear::removeDirector
     */
    public function testRemoveDirector(): void
    {
        $this->entityCollectionRemoveTest('director', 'User');
    }

    /**
     * @covers \App\Entity\ProgramYear::getDirectors
     */
    public function testGetDirectors(): void
    {
        $this->entityCollectionSetTest('director', 'User');
    }

    /**
     * @covers \App\Entity\ProgramYear::getSchool
     */
    public function testGetSchool(): void
    {
        $school = new School();
        $program = new Program();
        $program->setSchool($school);
        $programYear = new ProgramYear();
        $programYear->setProgram($program);
        $this->assertEquals($school, $programYear->getSchool());
    }

    /**
     * @covers \App\Entity\ProgramYear::addTerm
     */
    public function testAddTerm(): void
    {
        $this->entityCollectionAddTest('term', 'Term');
    }

    /**
     * @covers \App\Entity\ProgramYear::removeTerm
     */
    public function testRemoveTerm(): void
    {
        $this->entityCollectionRemoveTest('term', 'Term');
    }

    /**
     * @covers \App\Entity\ProgramYear::getTerms
     * @covers \App\Entity\ProgramYear::setTerms
     */
    public function testSetTerms(): void
    {
        $this->entityCollectionSetTest('term', 'Term');
    }

    /**
     * @covers \App\Entity\ProgramYear::addProgramYearObjective
     */
    public function testAddProgramYearObjective(): void
    {
        $this->entityCollectionAddTest('programYearObjective', 'ProgramYearObjective');
    }

    /**
     * @covers \App\Entity\ProgramYear::removeProgramYearObjective
     */
    public function testRemoveProgramYearObjective(): void
    {
        $this->entityCollectionRemoveTest('programYearObjective', 'ProgramYearObjective');
    }

    /**
     * @covers \App\Entity\ProgramYear::setProgramYearObjectives
     * @covers \App\Entity\ProgramYear::getProgramYearObjectives
     */
    public function testGetProgramYearObjectives(): void
    {
        $this->entityCollectionSetTest('programYearObjective', 'ProgramYearObjective');
    }

    /**
     * @covers \App\Entity\ProgramYear::addCompetency
     */
    public function testAddCompetency(): void
    {
        $this->entityCollectionAddTest('competencies', 'Competency', 'getCompetencies', 'addCompetency');
    }

    /**
     * @covers \App\Entity\ProgramYear::getCompetencies
     */
    public function testGetCompetencies(): void
    {
        $this->entityCollectionSetTest(
            'competencies',
            'Competency',
            'getCompetencies',
            'setCompetencies'
        );
    }

    /**
     * @covers \App\Entity\ProgramYear::removeCompetency
     */
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

    /**
     * @covers \App\Entity\ProgramYear::setCohort
     * @covers \App\Entity\ProgramYear::getCohort
     */
    public function testSetCohort(): void
    {
        $this->entitySetTest('cohort', 'Cohort');
    }

    protected function getObject(): ProgramYear
    {
        return $this->object;
    }
}
