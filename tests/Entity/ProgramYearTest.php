<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Objective;
use App\Entity\Program;
use App\Entity\ProgramYear;
use App\Entity\ProgramYearObjective;
use App\Entity\School;
use Mockery as m;

/**
 * Tests for Entity ProgramYear
 * @group model
 */
class ProgramYearTest extends EntityBase
{
    /**
     * @var ProgramYear
     */
    protected $object;

    /**
     * Instantiate a ProgramYear object
     */
    protected function setUp(): void
    {
        $this->object = new ProgramYear();
    }

    public function testNotBlankValidation()
    {
        $notBlank = [
            'startYear',
        ];
        $this->object->setProgram(m::mock('App\Entity\ProgramInterface'));

        $this->validateNotBlanks($notBlank);

        $this->object->setStartYear(3);
        $this->validate(0);
    }

    public function testNotNullValidation()
    {
        $notNull = [
            'program',
        ];
        $this->object->setStartYear(3);

        $this->validateNotNulls($notNull);
        $this->object->setProgram(m::mock('App\Entity\ProgramInterface'));


        $this->validate(0);
    }

    /**
     * @covers \App\Entity\ProgramYear::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getCompetencies());
        $this->assertEmpty($this->object->getDirectors());
        $this->assertEmpty($this->object->getProgramYearObjectives());
        $this->assertEmpty($this->object->getStewards());
        $this->assertEmpty($this->object->getTerms());
    }

    /**
     * @covers \App\Entity\ProgramYear::setStartYear
     * @covers \App\Entity\ProgramYear::getStartYear
     */
    public function testSetStartYear()
    {
        $this->basicSetTest('startYear', 'integer');
    }

    /**
     * @covers \App\Entity\ProgramYear::setLocked
     * @covers \App\Entity\ProgramYear::isLocked
     */
    public function testSetLocked()
    {
        $this->booleanSetTest('locked');
    }

    /**
     * @covers \App\Entity\ProgramYear::setArchived
     * @covers \App\Entity\ProgramYear::isArchived
     */
    public function testSetArchived()
    {
        $this->booleanSetTest('archived');
    }

    /**
     * @covers \App\Entity\ProgramYear::setPublishedAsTbd
     * @covers \App\Entity\ProgramYear::isPublishedAsTbd
     */
    public function testSetPublishedAsTbd()
    {
        $this->booleanSetTest('publishedAsTbd');
    }

    /**
     * @covers \App\Entity\ProgramYear::setPublished
     * @covers \App\Entity\ProgramYear::isPublished
     */
    public function testSetPublished()
    {
        $this->booleanSetTest('published');
    }

    /**
     * @covers \App\Entity\ProgramYear::setProgram
     * @covers \App\Entity\ProgramYear::getProgram
     */
    public function testSetProgram()
    {
        $this->entitySetTest('program', 'Program');
    }

    /**
     * @covers \App\Entity\ProgramYear::addDirector
     */
    public function testAddDirector()
    {
        $this->entityCollectionAddTest('director', 'User');
    }

    /**
     * @covers \App\Entity\ProgramYear::removeDirector
     */
    public function testRemoveDirector()
    {
        $this->entityCollectionRemoveTest('director', 'User');
    }

    /**
     * @covers \App\Entity\ProgramYear::getDirectors
     */
    public function testGetDirectors()
    {
        $this->entityCollectionSetTest('director', 'User');
    }

    /**
     * @covers \App\Entity\ProgramYear::addSteward
     */
    public function testAddSteward()
    {
        $this->entityCollectionAddTest('steward', 'ProgramYearSteward');
    }

    /**
     * @covers \App\Entity\ProgramYear::removeSteward
     */
    public function testRemoveSteward()
    {
        $this->entityCollectionRemoveTest('steward', 'ProgramYearSteward');
    }

    /**
     * @covers \App\Entity\ProgramYear::getStewards
     */
    public function testGetSteward()
    {
        $this->entityCollectionSetTest('steward', 'ProgramYearSteward');
    }

    /**
     * @covers \App\Entity\ProgramYear::getSchool
     */
    public function testGetSchool()
    {
        $school = new School();
        $program = new Program();
        $program->setSchool($school);
        $programYear = new ProgramYear();
        $programYear->setProgram($program);
        $this->assertEquals($school, $programYear->getSchool());

        $program = new Program();
        $programYear = new ProgramYear();
        $programYear->setProgram($program);
        $this->assertNull($programYear->getSchool());

        $programYear = new ProgramYear();
        $this->assertNull($programYear->getSchool());
    }

    /**
     * @covers \App\Entity\ProgramYear::addTerm
     */
    public function testAddTerm()
    {
        $this->entityCollectionAddTest('term', 'Term');
    }

    /**
     * @covers \App\Entity\ProgramYear::removeTerm
     */
    public function testRemoveTerm()
    {
        $this->entityCollectionRemoveTest('term', 'Term');
    }

    /**
     * @covers \App\Entity\ProgramYear::getTerms
     * @covers \App\Entity\ProgramYear::setTerms
     */
    public function testSetTerms()
    {
        $this->entityCollectionSetTest('term', 'Term');
    }

    /**
     * @covers \App\Entity\ProgramYear::addProgramYearObjective
     */
    public function testAddProgramYearObjective()
    {
        $this->entityCollectionAddTest('programYearObjective', 'ProgramYearObjective');
    }

    /**
     * @covers \App\Entity\ProgramYear::removeProgramYearObjective
     */
    public function testRemoveProgramYearObjective()
    {
        $this->entityCollectionRemoveTest('programYearObjective', 'ProgramYearObjective');
    }

    /**
     * @covers \App\Entity\ProgramYear::setProgramYearObjectives
     * @covers \App\Entity\ProgramYear::getProgramYearObjectives
     */
    public function testGetProgramYearObjectives()
    {
        $this->entityCollectionSetTest('programYearObjective', 'ProgramYearObjective');
    }

    /**
     * @covers \App\Entity\ProgramYear::addCompetency
     */
    public function testAddCompetency()
    {
        $this->entityCollectionAddTest('competencies', 'Competency', 'getCompetencies', 'addCompetency');
    }

    /**
     * @covers \App\Entity\ProgramYear::getCompetencies
     */
    public function testGetCompetencies()
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
    public function testRemoveCompetency()
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
    public function testSetCohort()
    {
        $this->entitySetTest('cohort', 'Cohort');
    }

    /**
     * @covers \App\Entity\ProgramYear:getObjectives
     */
    public function testGetObjectives()
    {
        $objective1 = new Objective();
        $objective2 = new Objective();
        $programYearObjective1 = new ProgramYearObjective();
        $programYearObjective1->setObjective($objective1);
        $programYearObjective2 = new ProgramYearObjective();
        $programYearObjective2->setObjective($objective2);
        $this->object->addProgramYearObjective($programYearObjective1);
        $this->object->addProgramYearObjective($programYearObjective2);
        $objectives = $this->object->getObjectives();
        $this->assertCount(2, $objectives);
        $this->assertContains($objective1, $objectives);
        $this->assertContains($objective2, $objectives);
    }
}
