<?php
namespace Ilios\CoreBundle\Tests\Entity;


use Ilios\CoreBundle\Entity\ProgramYear;
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

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getCompetencies());
        $this->assertEmpty($this->object->getDirectors());
        $this->assertEmpty($this->object->getDisciplines());
        $this->assertEmpty($this->object->getObjectives());
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::getProgramYearId
     */
    public function testGetProgramYearId()
    {
        $this->basicGetTest('programYearId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::setStartYear
     */
    public function testSetStartYear()
    {
        $this->basicSetTest('startYear', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::getStartYear
     */
    public function testGetStartYear()
    {
        $this->basicGetTest('startYear', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::setDeleted
     */
    public function testSetDeleted()
    {
        $this->basicSetTest('deleted', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::getDeleted
     */
    public function testGetDeleted()
    {
        $this->basicGetTest('deleted', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::setLocked
     */
    public function testSetLocked()
    {
        $this->basicSetTest('locked', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::getLocked
     */
    public function testGetLocked()
    {
        $this->basicGetTest('locked', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::setArchived
     */
    public function testSetArchived()
    {
        $this->basicSetTest('archived', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::getArchived
     */
    public function testGetArchived()
    {
        $this->basicGetTest('archived', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::setPublishedAsTbd
     */
    public function testSetPublishedAsTbd()
    {
        $this->basicSetTest('publishedAsTbd', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::getPublishedAsTbd
     */
    public function testGetPublishedAsTbd()
    {
        $this->basicGetTest('publishedAsTbd', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::setProgram
     */
    public function testSetProgram()
    {
        $this->entitySetTest('program', 'Program');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::getProgram
     */
    public function testGetProgram()
    {
        $this->entityGetTest('program', 'Program');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::addDirector
     */
    public function testAddDirector()
    {
        $this->entityCollectionAddTest('director', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::removeDirector
     */
    public function testRemoveDirector()
    {
        $this->entityCollectionRemoveTest('director', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::getDirectors
     */
    public function testGetDirectors()
    {
        $this->entityCollectionGetTest('director', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::addCompetency
     */
    public function testAddCompetency()
    {
        $this->entityCollectionAddTest('competencies', 'Competency', 'getCompetencies', 'AddCompetency');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::removeCompetency
     */
    public function testRemoveCompetency()
    {
        $this->entityCollectionRemoveTest(
            'competencies',
            'Competency',
            'getCompetencies',
            'AddCompetency',
            'removeCompetency'
        );
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::getCompetencies
     */
    public function testGetCompetencies()
    {
        $this->entityCollectionGetTest('competencies', 'Competency', 'getCompetencies', false);
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::addDiscipline
     */
    public function testAddDiscipline()
    {
        $this->entityCollectionAddTest('discipline', 'Discipline');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::removeDiscipline
     */
    public function testRemoveDiscipline()
    {
        $this->entityCollectionRemoveTest('discipline', 'Discipline');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::getDisciplines
     */
    public function testGetDisciplines()
    {
        $this->entityCollectionGetTest('discipline', 'Discipline');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::addObjective
     */
    public function testAddObjective()
    {
        $this->entityCollectionAddTest('objective', 'Objective');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::removeObjective
     */
    public function testRemoveObjective()
    {
        $this->entityCollectionRemoveTest('objective', 'Objective');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::getObjectives
     */
    public function testGetObjectives()
    {
        $this->entityCollectionGetTest('objective', 'Objective');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::getPublishEvent
     */
    public function testGetPublishEvent()
    {
         $this->entityGetTest('publishEvent', 'PublishEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::setPublishEvent
     */
    public function testSetPublishEvent()
    {
        $this->entitySetTest('publishEvent', 'PublishEvent');
    }
}
