<?php
namespace App\Tests\Entity;

use App\Entity\Course;
use App\Entity\School;
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
     * @covers \App\Entity\Course::__construct
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
     * @covers \App\Entity\Course::setTitle
     * @covers \App\Entity\Course::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\Course::setLevel
     * @covers \App\Entity\Course::getLevel
     */
    public function testSetCourseLevel()
    {
        $this->basicSetTest('level', 'integer');
    }

    /**
     * @covers \App\Entity\Course::setYear
     * @covers \App\Entity\Course::getYear
     */
    public function testSetYear()
    {
        $this->basicSetTest('year', 'integer');
    }

    /**
     * @covers \App\Entity\Course::setStartDate
     * @covers \App\Entity\Course::getStartDate
     */
    public function testSetStartDate()
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    /**
     * @covers \App\Entity\Course::setEndDate
     * @covers \App\Entity\Course::getEndDate
     */
    public function testSetEndDate()
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    /**
     * @covers \App\Entity\Course::setId
     * @covers \App\Entity\Course::getId
     */
    public function testSetExternalId()
    {
        $this->basicSetTest('externalId', 'string');
    }

    /**
     * @covers \App\Entity\Course::setLocked
     * @covers \App\Entity\Course::isLocked
     */
    public function testSetLocked()
    {
        $this->booleanSetTest('locked');
    }

    /**
     * @covers \App\Entity\Course::setArchived
     * @covers \App\Entity\Course::isArchived
     */
    public function testSetArchived()
    {
        $this->booleanSetTest('archived');
    }

    /**
     * @covers \App\Entity\Course::setPublishedAsTbd
     * @covers \App\Entity\Course::isPublishedAsTbd
     */
    public function testSetPublishedAsTbd()
    {
        $this->booleanSetTest('publishedAsTbd');
    }

    /**
     * @covers \App\Entity\Course::setPublished
     * @covers \App\Entity\Course::isPublished
     */
    public function testSetPublished()
    {
        $this->booleanSetTest('published');
    }

    /**
     * @covers \App\Entity\Course::setSchool
     * @covers \App\Entity\Course::getSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }

    /**
     * @covers \App\Entity\Course::setClerkshipType
     * @covers \App\Entity\Course::getClerkshipType
     */
    public function testSetClerkshipType()
    {
         $this->entitySetTest('clerkshipType', 'CourseClerkshipType');
    }

    /**
     * @covers \App\Entity\Course::addDirector
     */
    public function testAddDirector()
    {
        $this->entityCollectionAddTest('director', 'User', false, false, 'addDirectedCourse');
    }

    /**
     * @covers \App\Entity\Course::removeDirector
     */
    public function testRemoveDirector()
    {
        $this->entityCollectionRemoveTest('director', 'User', false, false, false, 'removeDirectedCourse');
    }

    /**
     * @covers \App\Entity\Course::getDirectors
     */
    public function testGetDirectors()
    {
        $this->entityCollectionSetTest('director', 'User', false, false, 'addDirectedCourse');
    }

    /**
     * @covers \App\Entity\Course::addCohort
     */
    public function testAddCohort()
    {
        $this->entityCollectionAddTest('cohort', 'Cohort', false, false, 'addCourse');
    }

    /**
     * @covers \App\Entity\Course::removeCohort
     */
    public function testRemoveCohort()
    {
        $this->entityCollectionRemoveTest('cohort', 'Cohort', false, false, false, 'removeCourse');
    }

    /**
     * @covers \App\Entity\Course::getCohorts
     */
    public function testGetCohorts()
    {
        $this->entityCollectionSetTest('cohort', 'Cohort', false, false, 'addCourse');
    }

    /**
     * @covers \App\Entity\Course::addLearningMaterial
     */
    public function testAddLearningMaterial()
    {
        $this->entityCollectionAddTest('learningMaterial', 'CourseLearningMaterial');
    }

    /**
     * @covers \App\Entity\Course::removeLearningMaterial
     */
    public function testRemoveLearningMaterial()
    {
        $this->entityCollectionRemoveTest('learningMaterial', 'CourseLearningMaterial');
    }

    /**
     * @covers \App\Entity\Course::setLearningMaterials
     * @covers \App\Entity\Course::getLearningMaterials
     */
    public function testGetLearningMaterials()
    {
        $this->entityCollectionSetTest('learningMaterial', 'CourseLearningMaterial');
    }

    /**
     * @covers \App\Entity\Course::addTerm
     */
    public function testAddTerm()
    {
        $this->entityCollectionAddTest('term', 'Term', false, false, 'addCourse');
    }

    /**
     * @covers \App\Entity\Course::removeTerm
     */
    public function testRemoveTerm()
    {
        $this->entityCollectionRemoveTest('term', 'Term', false, false, false, 'removeCourse');
    }

    /**
     * @covers \App\Entity\Course::getTerms
     * @covers \App\Entity\Course::setTerms
     */
    public function testSetTerms()
    {
        $this->entityCollectionSetTest('term', 'Term', false, false, 'addCourse');
    }

    /**
     * @covers \App\Entity\Course::setAncestor
     * @covers \App\Entity\Course::getAncestor
     */
    public function testSetAncestor()
    {
        $this->entitySetTest('ancestor', 'Course');
    }

    /**
     * @covers \App\Entity\Course::getAncestorOrSelf
     */
    public function testGetAncestorOrSelfWithAncestor()
    {
        $ancestor = m::mock('App\Entity\Course');
        $this->object->setAncestor($ancestor);
        $this->assertSame($ancestor, $this->object->getAncestorOrSelf());
    }

    /**
     * @covers \App\Entity\Course::getAncestorOrSelf
     */
    public function testGetAncestorOrSelfWithNoAncestor()
    {
        $this->assertSame($this->object, $this->object->getAncestorOrSelf());
    }

    /**
     * @covers \App\Entity\Course::addDescendant
     */
    public function testAddDescendant()
    {
        $this->entityCollectionAddTest('descendant', 'Course');
    }

    /**
     * @covers \App\Entity\Course::removeDescendant
     */
    public function testRemoveDescendant()
    {
        $this->entityCollectionRemoveTest('descendant', 'Course');
    }

    /**
     * @covers \App\Entity\Course::getDescendants
     * @covers \App\Entity\Course::setDescendants
     */
    public function testGetDescendants()
    {
        $this->entityCollectionSetTest('descendant', 'Course');
    }

    /**
     * @covers \App\Entity\Course::addSession
     */
    public function testAddSession()
    {
        $this->entityCollectionAddTest('session', 'Session');
    }

    /**
     * @covers \App\Entity\Course::removeSession
     */
    public function testRemoveSession()
    {
        $this->entityCollectionRemoveTest('session', 'Session');
    }

    /**
     * @covers \App\Entity\Course::getSessions
     */
    public function testGetSessions()
    {
        $this->entityCollectionSetTest('session', 'Session');
    }

    /**
     * @covers \App\Entity\Course::addAdministrator
     */
    public function testAddAdministrator()
    {
        $this->entityCollectionAddTest('administrator', 'User', false, false, 'addAdministeredCourse');
    }

    /**
     * @covers \App\Entity\Course::removeAdministrator
     */
    public function testRemoveAdministrator()
    {
        $this->entityCollectionRemoveTest('administrator', 'User', false, false, false, 'removeAdministeredCourse');
    }

    /**
     * @covers \App\Entity\Course::getAdministrators
     * @covers \App\Entity\Course::setAdministrators
     */
    public function testSetAdministrators()
    {
        $this->entityCollectionSetTest('administrator', 'User', false, false, 'addAdministeredCourse');
    }

    /**
     * @covers \App\Entity\Course::addObjective
     */
    public function testAddObjective()
    {
        $this->entityCollectionAddTest('objective', 'Objective', false, false, 'addCourse');
    }

    /**
     * @covers \App\Entity\Course::removeObjective
     */
    public function testRemoveObjective()
    {
        $this->entityCollectionRemoveTest('objective', 'Objective', false, false, false, 'removeCourse');
    }

    /**
     * @covers \App\Entity\Course::getObjectives
     */
    public function testGetObjectives()
    {
        $this->entityCollectionSetTest('objective', 'Objective', false, false, 'addCourse');
    }

    /**
     * @covers \App\Entity\Course::removeObjective
     */
    public function testRemoveObjectiveWithSessionChildren()
    {
        $sessionObjective = m::mock('App\Entity\Objective');
        $session = m::mock('App\Entity\Session');
        $this->object->addSession($session);
        $courseObjective = m::mock('App\Entity\Objective');
        $courseObjective->shouldReceive('addCourse')->with($this->object)->once();
        $courseObjective->shouldReceive('removeCourse')->with($this->object)->once();

        $session->shouldReceive('getObjectives')->andReturn([$sessionObjective])->once();
        $sessionObjective->shouldReceive('removeParent')->with($courseObjective)->once();
        $this->object->addObjective($courseObjective);
        $this->object->removeObjective($courseObjective);
    }

    /**
     * @covers \App\Entity\Course::addSequenceBlock
     */
    public function testAddSequenceBlock()
    {
        $this->entityCollectionAddTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \App\Entity\Course::removeSequenceBlock
     */
    public function testRemoveSequenceBlock()
    {
        $this->entityCollectionRemoveTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \App\Entity\Course::getSequenceBlocks
     * @covers \App\Entity\Course::setSequenceBlocks
     */
    public function testSetSequenceBlocks()
    {
        $this->entityCollectionSetTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }
}
