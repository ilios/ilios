<?php
namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\Course;
use Ilios\CoreBundle\Entity\School;
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

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'title',
            'level',
            'year',
            'startDate',
            'endDate',
        );
        $this->object->setSchool(new School());
        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('test');
        $this->object->setLevel(3);
        $this->object->setYear(2004);
        $this->object->setStartDate(new \DateTime());
        $this->object->setEndDate(new \DateTime());
        $this->validate(0);
    }

    public function testNotNullValidation()
    {
        $notNull = array(
            'school',
        );
        $this->object->setTitle('test');
        $this->object->setLevel(3);
        $this->object->setYear(2004);
        $this->object->setStartDate(new \DateTime());
        $this->object->setEndDate(new \DateTime());
        $this->validateNotNulls($notNull);

        $this->object->setSchool(new School());
        $this->validate(0);
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getCohorts());
        $this->assertEmpty($this->object->getDirectors());
        $this->assertEmpty($this->object->getMeshDescriptors());
        $this->assertEmpty($this->object->getObjectives());
        $this->assertEmpty($this->object->getLearningMaterials());
        $this->assertEmpty($this->object->getSessions());
        $this->assertEmpty($this->object->getTerms());
        $this->assertEmpty($this->object->getDescendants());
        $this->assertEmpty($this->object->getAdministrators());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::setTitle
     * @covers \Ilios\CoreBundle\Entity\Course::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::setLevel
     * @covers \Ilios\CoreBundle\Entity\Course::getLevel
     */
    public function testSetCourseLevel()
    {
        $this->basicSetTest('level', 'integer');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::setYear
     * @covers \Ilios\CoreBundle\Entity\Course::getYear
     */
    public function testSetYear()
    {
        $this->basicSetTest('year', 'integer');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::setStartDate
     * @covers \Ilios\CoreBundle\Entity\Course::getStartDate
     */
    public function testSetStartDate()
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::setEndDate
     * @covers \Ilios\CoreBundle\Entity\Course::getEndDate
     */
    public function testSetEndDate()
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::setId
     * @covers \Ilios\CoreBundle\Entity\Course::getId
     */
    public function testSetExternalId()
    {
        $this->basicSetTest('externalId', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::setLocked
     * @covers \Ilios\CoreBundle\Entity\Course::isLocked
     */
    public function testSetLocked()
    {
        $this->booleanSetTest('locked');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::setArchived
     * @covers \Ilios\CoreBundle\Entity\Course::isArchived
     */
    public function testSetArchived()
    {
        $this->booleanSetTest('archived');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::setPublishedAsTbd
     * @covers \Ilios\CoreBundle\Entity\Course::isPublishedAsTbd
     */
    public function testSetPublishedAsTbd()
    {
        $this->booleanSetTest('publishedAsTbd');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::setPublished
     * @covers \Ilios\CoreBundle\Entity\Course::isPublished
     */
    public function testSetPublished()
    {
        $this->booleanSetTest('published');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::setSchool
     * @covers \Ilios\CoreBundle\Entity\Course::getSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::setClerkshipType
     * @covers \Ilios\CoreBundle\Entity\Course::getClerkshipType
     */
    public function testSetClerkshipType()
    {
         $this->entitySetTest('clerkshipType', 'CourseClerkshipType');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::addDirector
     */
    public function testAddDirector()
    {
        $this->entityCollectionAddTest('director', 'User', false, false, 'addDirectedCourse');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::removeDirector
     */
    public function testRemoveDirector()
    {
        $this->entityCollectionRemoveTest('director', 'User', false, false, false, 'removeDirectedCourse');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::getDirectors
     */
    public function testGetDirectors()
    {
        $this->entityCollectionSetTest('director', 'User', false, false, 'addDirectedCourse');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::addCohort
     */
    public function testAddCohort()
    {
        $this->entityCollectionAddTest('cohort', 'Cohort', false, false, 'addCourse');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::removeCohort
     */
    public function testRemoveCohort()
    {
        $this->entityCollectionRemoveTest('cohort', 'Cohort', false, false, false, 'removeCourse');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::getCohorts
     */
    public function testGetCohorts()
    {
        $this->entityCollectionSetTest('cohort', 'Cohort', false, false, 'addCourse');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::addLearningMaterial
     */
    public function testAddLearningMaterial()
    {
        $this->entityCollectionAddTest('learningMaterial', 'CourseLearningMaterial');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::removeLearningMaterial
     */
    public function testRemoveLearningMaterial()
    {
        $this->entityCollectionRemoveTest('learningMaterial', 'CourseLearningMaterial');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::setLearningMaterials
     * @covers \Ilios\CoreBundle\Entity\Course::getLearningMaterials
     */
    public function testGetLearningMaterials()
    {
        $this->entityCollectionSetTest('learningMaterial', 'CourseLearningMaterial');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::addTerm
     */
    public function testAddTerm()
    {
        $this->entityCollectionAddTest('term', 'Term', false, false, 'addCourse');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::removeTerm
     */
    public function testRemoveTerm()
    {
        $this->entityCollectionRemoveTest('term', 'Term', false, false, false, 'removeCourse');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::getTerms
     * @covers \Ilios\CoreBundle\Entity\Course::setTerms
     */
    public function testSetTerms()
    {
        $this->entityCollectionSetTest('term', 'Term', false, false, 'addCourse');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::setAncestor
     * @covers \Ilios\CoreBundle\Entity\Course::getAncestor
     */
    public function testSetAncestor()
    {
        $this->entitySetTest('ancestor', 'Course');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::getAncestorOrSelf
     */
    public function testGetAncestorOrSelfWithAncestor()
    {
        $ancestor = m::mock('Ilios\CoreBundle\Entity\Course');
        $this->object->setAncestor($ancestor);
        $this->assertSame($ancestor, $this->object->getAncestorOrSelf());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::getAncestorOrSelf
     */
    public function testGetAncestorOrSelfWithNoAncestor()
    {
        $this->assertSame($this->object, $this->object->getAncestorOrSelf());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::addDescendant
     */
    public function testAddDescendant()
    {
        $this->entityCollectionAddTest('descendant', 'Course');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::removeDescendant
     */
    public function testRemoveDescendant()
    {
        $this->entityCollectionRemoveTest('descendant', 'Course');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::getDescendants
     * @covers \Ilios\CoreBundle\Entity\Course::setDescendants
     */
    public function testGetDescendants()
    {
        $this->entityCollectionSetTest('descendant', 'Course');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::addSession
     */
    public function testAddSession()
    {
        $this->entityCollectionAddTest('session', 'Session');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::removeSession
     */
    public function testRemoveSession()
    {
        $this->entityCollectionRemoveTest('session', 'Session');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::getSessions
     */
    public function testGetSessions()
    {
        $this->entityCollectionSetTest('session', 'Session');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::addAdministrator
     */
    public function testAddAdministrator()
    {
        $this->entityCollectionAddTest('administrator', 'User', false, false, 'addAdministeredCourse');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::removeAdministrator
     */
    public function testRemoveAdministrator()
    {
        $this->entityCollectionRemoveTest('administrator', 'User', false, false, false, 'removeAdministeredCourse');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::getAdministrators
     * @covers \Ilios\CoreBundle\Entity\Course::setAdministrators
     */
    public function testSetAdministrators()
    {
        $this->entityCollectionSetTest('administrator', 'User', false, false, 'addAdministeredCourse');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::addObjective
     */
    public function testAddObjective()
    {
        $this->entityCollectionAddTest('objective', 'Objective', false, false, 'addCourse');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::removeObjective
     */
    public function testRemoveObjective()
    {
        $this->entityCollectionRemoveTest('objective', 'Objective', false, false, false, 'removeCourse');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::getObjectives
     */
    public function testGetObjectives()
    {
        $this->entityCollectionSetTest('objective', 'Objective', false, false, 'addCourse');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Course::removeObjective
     */
    public function testRemoveObjectiveWithSessionChildren()
    {
        $sessionObjective = m::mock('Ilios\CoreBundle\Entity\Objective');
        $session = m::mock('Ilios\CoreBundle\Entity\Session');
        $this->object->addSession($session);
        $courseObjective = m::mock('Ilios\CoreBundle\Entity\Objective');
        $courseObjective->shouldReceive('addCourse')->with($this->object)->once();
        $courseObjective->shouldReceive('removeCourse')->with($this->object)->once();

        $session->shouldReceive('getObjectives')->andReturn([$sessionObjective])->once();
        $sessionObjective->shouldReceive('removeParent')->with($courseObjective)->once();
        $this->object->addObjective($courseObjective);
        $this->object->removeObjective($courseObjective);
    }
}
