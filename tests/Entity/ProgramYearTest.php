<?php
namespace App\Tests\Entity;

use App\Entity\Program;
use App\Entity\ProgramYear;
use App\Entity\School;
use Mockery as m;

/**
 * Tests for Entity ProgramYear
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
    protected function setUp()
    {
        $this->object = new ProgramYear;
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'startYear',
        );
        $this->object->setProgram(m::mock('App\Entity\ProgramInterface'));

        $this->validateNotBlanks($notBlank);

        $this->object->setStartYear(3);
        $this->validate(0);
    }

    public function testNotNullValidation()
    {
        $notNull = array(
            'program',
        );
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
        $this->assertEmpty($this->object->getObjectives());
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
     * @covers \App\Entity\ProgramYear::addObjective
     */
    public function testAddObjective()
    {
        $this->entityCollectionAddTest('objective', 'Objective');
    }

    /**
     * @covers \App\Entity\ProgramYear::removeObjective
     */
    public function testRemoveObjective()
    {
        $this->entityCollectionRemoveTest('objective', 'Objective');
    }

    /**
     * @covers \App\Entity\ProgramYear::getObjectives
     * @covers \App\Entity\ProgramYear::setObjectives
     */
    public function testSetObjectives()
    {
        $this->entityCollectionSetTest('objective', 'Objective');
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
}
