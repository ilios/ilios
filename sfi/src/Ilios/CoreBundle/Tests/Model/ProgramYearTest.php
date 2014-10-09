<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\ProgramYear;
use Mockery as m;

/**
 * Tests for Model ProgramYear
 */
class ProgramYearTest extends ModelBase
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

    /**
     * @covers Ilios\CoreBundle\Model\ProgramYear::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getCompetencies());
        $this->assertEmpty($this->object->getDirectors());
        $this->assertEmpty($this->object->getDisciplines());
        $this->assertEmpty($this->object->getObjectives());
    }

    /**
     * @covers Ilios\CoreBundle\Model\ProgramYear::getProgramYearId
     */
    public function testGetProgramYearId()
    {
        $this->basicGetTest('programYearId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ProgramYear::setStartYear
     */
    public function testSetStartYear()
    {
        $this->basicSetTest('startYear', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ProgramYear::getStartYear
     */
    public function testGetStartYear()
    {
        $this->basicGetTest('startYear', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ProgramYear::setDeleted
     */
    public function testSetDeleted()
    {
        $this->basicSetTest('deleted', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ProgramYear::getDeleted
     */
    public function testGetDeleted()
    {
        $this->basicGetTest('deleted', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ProgramYear::setLocked
     */
    public function testSetLocked()
    {
        $this->basicSetTest('locked', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ProgramYear::getLocked
     */
    public function testGetLocked()
    {
        $this->basicGetTest('locked', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ProgramYear::setArchived
     */
    public function testSetArchived()
    {
        $this->basicSetTest('archived', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ProgramYear::getArchived
     */
    public function testGetArchived()
    {
        $this->basicGetTest('archived', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ProgramYear::setPublishedAsTbd
     */
    public function testSetPublishedAsTbd()
    {
        $this->basicSetTest('publishedAsTbd', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ProgramYear::getPublishedAsTbd
     */
    public function testGetPublishedAsTbd()
    {
        $this->basicGetTest('publishedAsTbd', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ProgramYear::setProgram
     */
    public function testSetProgram()
    {
        $this->modelSetTest('program', 'Program');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ProgramYear::getProgram
     */
    public function testGetProgram()
    {
        $this->modelGetTest('program', 'Program');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ProgramYear::addDirector
     */
    public function testAddDirector()
    {
        $this->modelCollectionAddTest('director', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ProgramYear::removeDirector
     */
    public function testRemoveDirector()
    {
        $this->modelCollectionRemoveTest('director', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ProgramYear::getDirectors
     */
    public function testGetDirectors()
    {
        $this->modelCollectionGetTest('director', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ProgramYear::addCompetency
     */
    public function testAddCompetency()
    {
        $this->modelCollectionAddTest('competencies', 'Competency', 'getCompetencies', 'AddCompetency');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ProgramYear::removeCompetency
     */
    public function testRemoveCompetency()
    {
        $this->modelCollectionRemoveTest(
            'competencies',
            'Competency',
            'getCompetencies',
            'AddCompetency',
            'removeCompetency'
        );
    }

    /**
     * @covers Ilios\CoreBundle\Model\ProgramYear::getCompetencies
     */
    public function testGetCompetencies()
    {
        $this->modelCollectionGetTest('competencies', 'Competency', 'getCompetencies', false);
    }

    /**
     * @covers Ilios\CoreBundle\Model\ProgramYear::addDiscipline
     */
    public function testAddDiscipline()
    {
        $this->modelCollectionAddTest('discipline', 'Discipline');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ProgramYear::removeDiscipline
     */
    public function testRemoveDiscipline()
    {
        $this->modelCollectionRemoveTest('discipline', 'Discipline');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ProgramYear::getDisciplines
     */
    public function testGetDisciplines()
    {
        $this->modelCollectionGetTest('discipline', 'Discipline');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ProgramYear::addObjective
     */
    public function testAddObjective()
    {
        $this->modelCollectionAddTest('objective', 'Objective');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ProgramYear::removeObjective
     */
    public function testRemoveObjective()
    {
        $this->modelCollectionRemoveTest('objective', 'Objective');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ProgramYear::getObjectives
     */
    public function testGetObjectives()
    {
        $this->modelCollectionGetTest('objective', 'Objective');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ProgramYear::getPublishEvent
     */
    public function testGetPublishEvent()
    {
         $this->modelGetTest('publishEvent', 'PublishEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ProgramYear::setPublishEvent
     */
    public function testSetPublishEvent()
    {
        $this->modelSetTest('publishEvent', 'PublishEvent');
    }
}
