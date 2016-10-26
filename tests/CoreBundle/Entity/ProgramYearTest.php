<?php
namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\Program;
use Ilios\CoreBundle\Entity\ProgramYear;
use Ilios\CoreBundle\Entity\School;
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
        $this->object->setProgram(m::mock('Ilios\CoreBundle\Entity\ProgramInterface'));

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
        $this->object->setProgram(m::mock('Ilios\CoreBundle\Entity\ProgramInterface'));


        $this->validate(0);
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\ProgramYear::__construct
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
     * @covers \Ilios\CoreBundle\Entity\ProgramYear::setStartYear
     * @covers \Ilios\CoreBundle\Entity\ProgramYear::getStartYear
     */
    public function testSetStartYear()
    {
        $this->basicSetTest('startYear', 'integer');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\ProgramYear::setLocked
     * @covers \Ilios\CoreBundle\Entity\ProgramYear::isLocked
     */
    public function testSetLocked()
    {
        $this->booleanSetTest('locked');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\ProgramYear::setArchived
     * @covers \Ilios\CoreBundle\Entity\ProgramYear::isArchived
     */
    public function testSetArchived()
    {
        $this->booleanSetTest('archived');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\ProgramYear::setPublishedAsTbd
     * @covers \Ilios\CoreBundle\Entity\ProgramYear::isPublishedAsTbd
     */
    public function testSetPublishedAsTbd()
    {
        $this->booleanSetTest('publishedAsTbd');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\ProgramYear::setPublished
     * @covers \Ilios\CoreBundle\Entity\ProgramYear::isPublished
     */
    public function testSetPublished()
    {
        $this->booleanSetTest('published');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\ProgramYear::setProgram
     * @covers \Ilios\CoreBundle\Entity\ProgramYear::getProgram
     */
    public function testSetProgram()
    {
        $this->entitySetTest('program', 'Program');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\ProgramYear::addDirector
     */
    public function testAddDirector()
    {
        $this->entityCollectionAddTest('director', 'User');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\ProgramYear::removeDirector
     */
    public function testRemoveDirector()
    {
        $this->entityCollectionRemoveTest('director', 'User');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\ProgramYear::getDirectors
     */
    public function testGetDirectors()
    {
        $this->entityCollectionSetTest('director', 'User');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\ProgramYear::addSteward
     */
    public function testAddSteward()
    {
        $this->entityCollectionAddTest('steward', 'ProgramYearSteward');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\ProgramYear::removeSteward
     */
    public function testRemoveSteward()
    {
        $this->entityCollectionRemoveTest('steward', 'ProgramYearSteward');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\ProgramYear::getStewards
     */
    public function testGetSteward()
    {
        $this->entityCollectionSetTest('steward', 'ProgramYearSteward');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\ProgramYear::getSchool
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
     * @covers \Ilios\CoreBundle\Entity\ProgramYear::addTerm
     */
    public function testAddTerm()
    {
        $this->entityCollectionAddTest('term', 'Term');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\ProgramYear::removeTerm
     */
    public function testRemoveTerm()
    {
        $this->entityCollectionRemoveTest('term', 'Term');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\ProgramYear::getTerms
     * @covers \Ilios\CoreBundle\Entity\ProgramYear::setTerms
     */
    public function testSetTerms()
    {
        $this->entityCollectionSetTest('term', 'Term');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\ProgramYear::addObjective
     */
    public function testAddObjective()
    {
        $this->entityCollectionAddTest('objective', 'Objective');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\ProgramYear::removeObjective
     */
    public function testRemoveObjective()
    {
        $this->entityCollectionRemoveTest('objective', 'Objective');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\ProgramYear::getObjectives
     * @covers \Ilios\CoreBundle\Entity\ProgramYear::setObjectives
     */
    public function testSetObjectives()
    {
        $this->entityCollectionSetTest('objective', 'Objective');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\ProgramYear::addCompetency
     */
    public function testAddCompetency()
    {
        $this->entityCollectionAddTest('competencies', 'Competency', 'getCompetencies', 'addCompetency');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\ProgramYear::getCompetencies
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
     * @covers \Ilios\CoreBundle\Entity\ProgramYear::removeCompetency
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
     * @covers \Ilios\CoreBundle\Entity\ProgramYear::setCohort
     * @covers \Ilios\CoreBundle\Entity\ProgramYear::getCohort
     */
    public function testSetCohort()
    {
        $this->entitySetTest('cohort', 'Cohort');
    }
}
