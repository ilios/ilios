<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\Course;
use Mockery as m;

/**
 * Tests for Model Course
 */
class CourseTest extends ModelBase
{
    /**
     * @var Course
     */
    protected $object;

    /**
     * Instantiate a Course object
     */
    protected function setUp()
    {
        $this->object = new Course;
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getCohorts());
        $this->assertEmpty($this->object->getDirectors());
        $this->assertEmpty($this->object->getDisciplines());
        $this->assertEmpty($this->object->getMeshDescriptors());
        $this->assertEmpty($this->object->getObjectives());
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::getCourseId
     */
    public function testGetCourseId()
    {
        $this->basicGetTest('courseId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::getTitle
     */
    public function testGetTitle()
    {
        $this->basicGetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::setCourseLevel
     */
    public function testSetCourseLevel()
    {
        $this->basicSetTest('courseLevel', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::getCourseLevel
     */
    public function testGetCourseLevel()
    {
        $this->basicGetTest('courseLevel', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::setYear
     */
    public function testSetYear()
    {
        $this->basicSetTest('year', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::getYear
     */
    public function testGetYear()
    {
        $this->basicGetTest('year', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::setStartDate
     */
    public function testSetStartDate()
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::getStartDate
     */
    public function testGetStartDate()
    {
        $this->basicGetTest('startDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::setEndDate
     */
    public function testSetEndDate()
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::getEndDate
     */
    public function testGetEndDate()
    {
        $this->basicGetTest('endDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::setDeleted
     */
    public function testSetDeleted()
    {
        $this->basicSetTest('deleted', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::getDeleted
     */
    public function testGetDeleted()
    {
        $this->basicGetTest('deleted', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::setExternalId
     */
    public function testSetExternalId()
    {
        $this->basicSetTest('externalId', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::getExternalId
     */
    public function testGetExternalId()
    {
        $this->basicGetTest('externalId', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::setLocked
     */
    public function testSetLocked()
    {
        $this->basicSetTest('locked', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::getLocked
     */
    public function testGetLocked()
    {
        $this->basicGetTest('locked', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::setArchived
     */
    public function testSetArchived()
    {
        $this->basicSetTest('archived', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::getArchived
     */
    public function testGetArchived()
    {
        $this->basicGetTest('archived', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::setOwningSchool
     */
    public function testSetOwningSchool()
    {
        $this->modelSetTest('owningSchool', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::getOwningSchool
     */
    public function testGetOwningSchool()
    {
         $this->modelGetTest('owningSchool', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::setPublishedAsTbd
     */
    public function testSetPublishedAsTbd()
    {
        $this->basicSetTest('publishedAsTbd', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::getPublishedAsTbd
     */
    public function testGetPublishedAsTbd()
    {
        $this->basicGetTest('publishedAsTbd', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::setClerkshipType
     */
    public function testSetClerkshipType()
    {
         $this->modelSetTest('clerkshipType', 'CourseClerkshipType');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::getClerkshipType
     */
    public function testGetClerkshipType()
    {
         $this->modelGetTest('clerkshipType', 'CourseClerkshipType');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::addDirector
     */
    public function testAddDirector()
    {
        $this->modelCollectionAddTest('director', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::removeDirector
     */
    public function testRemoveDirector()
    {
        $this->modelCollectionRemoveTest('director', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::getDirectors
     */
    public function testGetDirectors()
    {
        $this->modelCollectionGetTest('director', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::addCohort
     */
    public function testAddCohort()
    {
        $this->modelCollectionAddTest('cohort', 'Cohort');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::removeCohort
     */
    public function testRemoveCohort()
    {
        $this->modelCollectionRemoveTest('cohort', 'Cohort');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::getCohorts
     */
    public function testGetCohorts()
    {
        $this->modelCollectionGetTest('cohort', 'Cohort');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::addDiscipline
     */
    public function testAddDiscipline()
    {
        $this->modelCollectionAddTest('discipline', 'Discipline');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::removeDiscipline
     */
    public function testRemoveDiscipline()
    {
        $this->modelCollectionRemoveTest('discipline', 'Discipline');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::getDisciplines
     */
    public function testGetDisciplines()
    {
        $this->modelCollectionGetTest('discipline', 'Discipline');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::addObjective
     */
    public function testAddObjective()
    {
        $this->modelCollectionAddTest('objective', 'Objective');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::removeObjective
     */
    public function testRemoveObjective()
    {
        $this->modelCollectionRemoveTest('objective', 'Objective');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::getObjectives
     */
    public function testGetObjectives()
    {
        $this->modelCollectionGetTest('objective', 'Objective');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::addMeshDescriptor
     */
    public function testAddMeshDescriptor()
    {
        $this->modelCollectionAddTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::removeMeshDescriptor
     */
    public function testRemoveMeshDescriptor()
    {
        $this->modelCollectionRemoveTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::getMeshDescriptors
     */
    public function testGetMeshDescriptors()
    {
        $this->modelCollectionGetTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::getPublishEvent
     */
    public function testGetPublishEvent()
    {
         $this->modelGetTest('publishEvent', 'PublishEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Course::setPublishEvent
     */
    public function testSetPublishEvent()
    {
        $this->modelSetTest('publishEvent', 'PublishEvent');
    }
}
