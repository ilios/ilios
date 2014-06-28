<?php
namespace Ilios\CoreBundle\Tests\Entity;


use Ilios\CoreBundle\Entity\Course;
use Mockery as m;

/**
 * Tests for Entity Course
 */
class CourseTest extends EntityBase
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
     * @covers Ilios\CoreBundle\Entity\Course::__construct
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
     * @covers Ilios\CoreBundle\Entity\Course::getCourseId
     */
    public function testGetCourseId()
    {
        $this->basicGetTest('courseId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::getTitle
     */
    public function testGetTitle()
    {
        $this->basicGetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::setCourseLevel
     */
    public function testSetCourseLevel()
    {
        $this->basicSetTest('courseLevel', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::getCourseLevel
     */
    public function testGetCourseLevel()
    {
        $this->basicGetTest('courseLevel', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::setYear
     */
    public function testSetYear()
    {
        $this->basicSetTest('year', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::getYear
     */
    public function testGetYear()
    {
        $this->basicGetTest('year', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::setStartDate
     */
    public function testSetStartDate()
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::getStartDate
     */
    public function testGetStartDate()
    {
        $this->basicGetTest('startDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::setEndDate
     */
    public function testSetEndDate()
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::getEndDate
     */
    public function testGetEndDate()
    {
        $this->basicGetTest('endDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::setDeleted
     */
    public function testSetDeleted()
    {
        $this->basicSetTest('deleted', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::getDeleted
     */
    public function testGetDeleted()
    {
        $this->basicGetTest('deleted', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::setExternalId
     */
    public function testSetExternalId()
    {
        $this->basicSetTest('externalId', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::getExternalId
     */
    public function testGetExternalId()
    {
        $this->basicGetTest('externalId', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::setLocked
     */
    public function testSetLocked()
    {
        $this->basicSetTest('locked', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::getLocked
     */
    public function testGetLocked()
    {
        $this->basicGetTest('locked', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::setArchived
     */
    public function testSetArchived()
    {
        $this->basicSetTest('archived', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::getArchived
     */
    public function testGetArchived()
    {
        $this->basicGetTest('archived', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::setOwningSchool
     */
    public function testSetOwningSchool()
    {
        $this->entitySetTest('owningSchool', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::getOwningSchool
     */
    public function testGetOwningSchool()
    {
         $this->entityGetTest('owningSchool', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::setPublishedAsTbd
     */
    public function testSetPublishedAsTbd()
    {
        $this->basicSetTest('publishedAsTbd', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::getPublishedAsTbd
     */
    public function testGetPublishedAsTbd()
    {
        $this->basicGetTest('publishedAsTbd', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::setClerkshipType
     */
    public function testSetClerkshipType()
    {
         $this->entitySetTest('clerkshipType', 'CourseClerkshipType');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::getClerkshipType
     */
    public function testGetClerkshipType()
    {
         $this->entityGetTest('clerkshipType', 'CourseClerkshipType');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::addDirector
     */
    public function testAddDirector()
    {
        $this->entityCollectionAddTest('director', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::removeDirector
     */
    public function testRemoveDirector()
    {
        $this->entityCollectionRemoveTest('director', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::getDirectors
     */
    public function testGetDirectors()
    {
        $this->entityCollectionGetTest('director', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::addCohort
     */
    public function testAddCohort()
    {
        $this->entityCollectionAddTest('cohort', 'Cohort');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::removeCohort
     */
    public function testRemoveCohort()
    {
        $this->entityCollectionRemoveTest('cohort', 'Cohort');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::getCohorts
     */
    public function testGetCohorts()
    {
        $this->entityCollectionGetTest('cohort', 'Cohort');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::addDiscipline
     */
    public function testAddDiscipline()
    {
        $this->entityCollectionAddTest('discipline', 'Discipline');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::removeDiscipline
     */
    public function testRemoveDiscipline()
    {
        $this->entityCollectionRemoveTest('discipline', 'Discipline');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::getDisciplines
     */
    public function testGetDisciplines()
    {
        $this->entityCollectionGetTest('discipline', 'Discipline');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::addObjective
     */
    public function testAddObjective()
    {
        $this->entityCollectionAddTest('objective', 'Objective');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::removeObjective
     */
    public function testRemoveObjective()
    {
        $this->entityCollectionRemoveTest('objective', 'Objective');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::getObjectives
     */
    public function testGetObjectives()
    {
        $this->entityCollectionGetTest('objective', 'Objective');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::addMeshDescriptor
     */
    public function testAddMeshDescriptor()
    {
        $this->entityCollectionAddTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::removeMeshDescriptor
     */
    public function testRemoveMeshDescriptor()
    {
        $this->entityCollectionRemoveTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::getMeshDescriptors
     */
    public function testGetMeshDescriptors()
    {
        $this->entityCollectionGetTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::getPublishEvent
     */
    public function testGetPublishEvent()
    {
         $this->entityGetTest('publishEvent', 'PublishEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Course::setPublishEvent
     */
    public function testSetPublishEvent()
    {
        $this->entitySetTest('publishEvent', 'PublishEvent');
    }
}
