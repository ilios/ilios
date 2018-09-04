<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Program;
use AppBundle\Entity\ProgramYear;
use AppBundle\Entity\School;
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
        $this->object->setProgram(m::mock('AppBundle\Entity\ProgramInterface'));

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
        $this->object->setProgram(m::mock('AppBundle\Entity\ProgramInterface'));


        $this->validate(0);
    }

    /**
     * @covers \AppBundle\Entity\ProgramYear::__construct
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
     * @covers \AppBundle\Entity\ProgramYear::setStartYear
     * @covers \AppBundle\Entity\ProgramYear::getStartYear
     */
    public function testSetStartYear()
    {
        $this->basicSetTest('startYear', 'integer');
    }

    /**
     * @covers \AppBundle\Entity\ProgramYear::setLocked
     * @covers \AppBundle\Entity\ProgramYear::isLocked
     */
    public function testSetLocked()
    {
        $this->booleanSetTest('locked');
    }

    /**
     * @covers \AppBundle\Entity\ProgramYear::setArchived
     * @covers \AppBundle\Entity\ProgramYear::isArchived
     */
    public function testSetArchived()
    {
        $this->booleanSetTest('archived');
    }

    /**
     * @covers \AppBundle\Entity\ProgramYear::setPublishedAsTbd
     * @covers \AppBundle\Entity\ProgramYear::isPublishedAsTbd
     */
    public function testSetPublishedAsTbd()
    {
        $this->booleanSetTest('publishedAsTbd');
    }

    /**
     * @covers \AppBundle\Entity\ProgramYear::setPublished
     * @covers \AppBundle\Entity\ProgramYear::isPublished
     */
    public function testSetPublished()
    {
        $this->booleanSetTest('published');
    }

    /**
     * @covers \AppBundle\Entity\ProgramYear::setProgram
     * @covers \AppBundle\Entity\ProgramYear::getProgram
     */
    public function testSetProgram()
    {
        $this->entitySetTest('program', 'Program');
    }

    /**
     * @covers \AppBundle\Entity\ProgramYear::addDirector
     */
    public function testAddDirector()
    {
        $this->entityCollectionAddTest('director', 'User');
    }

    /**
     * @covers \AppBundle\Entity\ProgramYear::removeDirector
     */
    public function testRemoveDirector()
    {
        $this->entityCollectionRemoveTest('director', 'User');
    }

    /**
     * @covers \AppBundle\Entity\ProgramYear::getDirectors
     */
    public function testGetDirectors()
    {
        $this->entityCollectionSetTest('director', 'User');
    }

    /**
     * @covers \AppBundle\Entity\ProgramYear::addSteward
     */
    public function testAddSteward()
    {
        $this->entityCollectionAddTest('steward', 'ProgramYearSteward');
    }

    /**
     * @covers \AppBundle\Entity\ProgramYear::removeSteward
     */
    public function testRemoveSteward()
    {
        $this->entityCollectionRemoveTest('steward', 'ProgramYearSteward');
    }

    /**
     * @covers \AppBundle\Entity\ProgramYear::getStewards
     */
    public function testGetSteward()
    {
        $this->entityCollectionSetTest('steward', 'ProgramYearSteward');
    }

    /**
     * @covers \AppBundle\Entity\ProgramYear::getSchool
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
     * @covers \AppBundle\Entity\ProgramYear::addTerm
     */
    public function testAddTerm()
    {
        $this->entityCollectionAddTest('term', 'Term');
    }

    /**
     * @covers \AppBundle\Entity\ProgramYear::removeTerm
     */
    public function testRemoveTerm()
    {
        $this->entityCollectionRemoveTest('term', 'Term');
    }

    /**
     * @covers \AppBundle\Entity\ProgramYear::getTerms
     * @covers \AppBundle\Entity\ProgramYear::setTerms
     */
    public function testSetTerms()
    {
        $this->entityCollectionSetTest('term', 'Term');
    }

    /**
     * @covers \AppBundle\Entity\ProgramYear::addObjective
     */
    public function testAddObjective()
    {
        $this->entityCollectionAddTest('objective', 'Objective');
    }

    /**
     * @covers \AppBundle\Entity\ProgramYear::removeObjective
     */
    public function testRemoveObjective()
    {
        $this->entityCollectionRemoveTest('objective', 'Objective');
    }

    /**
     * @covers \AppBundle\Entity\ProgramYear::getObjectives
     * @covers \AppBundle\Entity\ProgramYear::setObjectives
     */
    public function testSetObjectives()
    {
        $this->entityCollectionSetTest('objective', 'Objective');
    }

    /**
     * @covers \AppBundle\Entity\ProgramYear::addCompetency
     */
    public function testAddCompetency()
    {
        $this->entityCollectionAddTest('competencies', 'Competency', 'getCompetencies', 'addCompetency');
    }

    /**
     * @covers \AppBundle\Entity\ProgramYear::getCompetencies
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
     * @covers \AppBundle\Entity\ProgramYear::removeCompetency
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
     * @covers \AppBundle\Entity\ProgramYear::setCohort
     * @covers \AppBundle\Entity\ProgramYear::getCohort
     */
    public function testSetCohort()
    {
        $this->entitySetTest('cohort', 'Cohort');
    }
}
