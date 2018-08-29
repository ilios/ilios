<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Course;
use AppBundle\Entity\School;
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
     * @covers \AppBundle\Entity\Course::__construct
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
     * @covers \AppBundle\Entity\Course::setTitle
     * @covers \AppBundle\Entity\Course::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \AppBundle\Entity\Course::setLevel
     * @covers \AppBundle\Entity\Course::getLevel
     */
    public function testSetCourseLevel()
    {
        $this->basicSetTest('level', 'integer');
    }

    /**
     * @covers \AppBundle\Entity\Course::setYear
     * @covers \AppBundle\Entity\Course::getYear
     */
    public function testSetYear()
    {
        $this->basicSetTest('year', 'integer');
    }

    /**
     * @covers \AppBundle\Entity\Course::setStartDate
     * @covers \AppBundle\Entity\Course::getStartDate
     */
    public function testSetStartDate()
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    /**
     * @covers \AppBundle\Entity\Course::setEndDate
     * @covers \AppBundle\Entity\Course::getEndDate
     */
    public function testSetEndDate()
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    /**
     * @covers \AppBundle\Entity\Course::setId
     * @covers \AppBundle\Entity\Course::getId
     */
    public function testSetExternalId()
    {
        $this->basicSetTest('externalId', 'string');
    }

    /**
     * @covers \AppBundle\Entity\Course::setLocked
     * @covers \AppBundle\Entity\Course::isLocked
     */
    public function testSetLocked()
    {
        $this->booleanSetTest('locked');
    }

    /**
     * @covers \AppBundle\Entity\Course::setArchived
     * @covers \AppBundle\Entity\Course::isArchived
     */
    public function testSetArchived()
    {
        $this->booleanSetTest('archived');
    }

    /**
     * @covers \AppBundle\Entity\Course::setPublishedAsTbd
     * @covers \AppBundle\Entity\Course::isPublishedAsTbd
     */
    public function testSetPublishedAsTbd()
    {
        $this->booleanSetTest('publishedAsTbd');
    }

    /**
     * @covers \AppBundle\Entity\Course::setPublished
     * @covers \AppBundle\Entity\Course::isPublished
     */
    public function testSetPublished()
    {
        $this->booleanSetTest('published');
    }

    /**
     * @covers \AppBundle\Entity\Course::setSchool
     * @covers \AppBundle\Entity\Course::getSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }

    /**
     * @covers \AppBundle\Entity\Course::setClerkshipType
     * @covers \AppBundle\Entity\Course::getClerkshipType
     */
    public function testSetClerkshipType()
    {
         $this->entitySetTest('clerkshipType', 'CourseClerkshipType');
    }

    /**
     * @covers \AppBundle\Entity\Course::addDirector
     */
    public function testAddDirector()
    {
        $this->entityCollectionAddTest('director', 'User', false, false, 'addDirectedCourse');
    }

    /**
     * @covers \AppBundle\Entity\Course::removeDirector
     */
    public function testRemoveDirector()
    {
        $this->entityCollectionRemoveTest('director', 'User', false, false, false, 'removeDirectedCourse');
    }

    /**
     * @covers \AppBundle\Entity\Course::getDirectors
     */
    public function testGetDirectors()
    {
        $this->entityCollectionSetTest('director', 'User', false, false, 'addDirectedCourse');
    }

    /**
     * @covers \AppBundle\Entity\Course::addCohort
     */
    public function testAddCohort()
    {
        $this->entityCollectionAddTest('cohort', 'Cohort', false, false, 'addCourse');
    }

    /**
     * @covers \AppBundle\Entity\Course::removeCohort
     */
    public function testRemoveCohort()
    {
        $this->entityCollectionRemoveTest('cohort', 'Cohort', false, false, false, 'removeCourse');
    }

    /**
     * @covers \AppBundle\Entity\Course::getCohorts
     */
    public function testGetCohorts()
    {
        $this->entityCollectionSetTest('cohort', 'Cohort', false, false, 'addCourse');
    }

    /**
     * @covers \AppBundle\Entity\Course::addLearningMaterial
     */
    public function testAddLearningMaterial()
    {
        $this->entityCollectionAddTest('learningMaterial', 'CourseLearningMaterial');
    }

    /**
     * @covers \AppBundle\Entity\Course::removeLearningMaterial
     */
    public function testRemoveLearningMaterial()
    {
        $this->entityCollectionRemoveTest('learningMaterial', 'CourseLearningMaterial');
    }

    /**
     * @covers \AppBundle\Entity\Course::setLearningMaterials
     * @covers \AppBundle\Entity\Course::getLearningMaterials
     */
    public function testGetLearningMaterials()
    {
        $this->entityCollectionSetTest('learningMaterial', 'CourseLearningMaterial');
    }

    /**
     * @covers \AppBundle\Entity\Course::addTerm
     */
    public function testAddTerm()
    {
        $this->entityCollectionAddTest('term', 'Term', false, false, 'addCourse');
    }

    /**
     * @covers \AppBundle\Entity\Course::removeTerm
     */
    public function testRemoveTerm()
    {
        $this->entityCollectionRemoveTest('term', 'Term', false, false, false, 'removeCourse');
    }

    /**
     * @covers \AppBundle\Entity\Course::getTerms
     * @covers \AppBundle\Entity\Course::setTerms
     */
    public function testSetTerms()
    {
        $this->entityCollectionSetTest('term', 'Term', false, false, 'addCourse');
    }

    /**
     * @covers \AppBundle\Entity\Course::setAncestor
     * @covers \AppBundle\Entity\Course::getAncestor
     */
    public function testSetAncestor()
    {
        $this->entitySetTest('ancestor', 'Course');
    }

    /**
     * @covers \AppBundle\Entity\Course::getAncestorOrSelf
     */
    public function testGetAncestorOrSelfWithAncestor()
    {
        $ancestor = m::mock('AppBundle\Entity\Course');
        $this->object->setAncestor($ancestor);
        $this->assertSame($ancestor, $this->object->getAncestorOrSelf());
    }

    /**
     * @covers \AppBundle\Entity\Course::getAncestorOrSelf
     */
    public function testGetAncestorOrSelfWithNoAncestor()
    {
        $this->assertSame($this->object, $this->object->getAncestorOrSelf());
    }

    /**
     * @covers \AppBundle\Entity\Course::addDescendant
     */
    public function testAddDescendant()
    {
        $this->entityCollectionAddTest('descendant', 'Course');
    }

    /**
     * @covers \AppBundle\Entity\Course::removeDescendant
     */
    public function testRemoveDescendant()
    {
        $this->entityCollectionRemoveTest('descendant', 'Course');
    }

    /**
     * @covers \AppBundle\Entity\Course::getDescendants
     * @covers \AppBundle\Entity\Course::setDescendants
     */
    public function testGetDescendants()
    {
        $this->entityCollectionSetTest('descendant', 'Course');
    }

    /**
     * @covers \AppBundle\Entity\Course::addSession
     */
    public function testAddSession()
    {
        $this->entityCollectionAddTest('session', 'Session');
    }

    /**
     * @covers \AppBundle\Entity\Course::removeSession
     */
    public function testRemoveSession()
    {
        $this->entityCollectionRemoveTest('session', 'Session');
    }

    /**
     * @covers \AppBundle\Entity\Course::getSessions
     */
    public function testGetSessions()
    {
        $this->entityCollectionSetTest('session', 'Session');
    }

    /**
     * @covers \AppBundle\Entity\Course::addAdministrator
     */
    public function testAddAdministrator()
    {
        $this->entityCollectionAddTest('administrator', 'User', false, false, 'addAdministeredCourse');
    }

    /**
     * @covers \AppBundle\Entity\Course::removeAdministrator
     */
    public function testRemoveAdministrator()
    {
        $this->entityCollectionRemoveTest('administrator', 'User', false, false, false, 'removeAdministeredCourse');
    }

    /**
     * @covers \AppBundle\Entity\Course::getAdministrators
     * @covers \AppBundle\Entity\Course::setAdministrators
     */
    public function testSetAdministrators()
    {
        $this->entityCollectionSetTest('administrator', 'User', false, false, 'addAdministeredCourse');
    }

    /**
     * @covers \AppBundle\Entity\Course::addObjective
     */
    public function testAddObjective()
    {
        $this->entityCollectionAddTest('objective', 'Objective', false, false, 'addCourse');
    }

    /**
     * @covers \AppBundle\Entity\Course::removeObjective
     */
    public function testRemoveObjective()
    {
        $this->entityCollectionRemoveTest('objective', 'Objective', false, false, false, 'removeCourse');
    }

    /**
     * @covers \AppBundle\Entity\Course::getObjectives
     */
    public function testGetObjectives()
    {
        $this->entityCollectionSetTest('objective', 'Objective', false, false, 'addCourse');
    }

    /**
     * @covers \AppBundle\Entity\Course::removeObjective
     */
    public function testRemoveObjectiveWithSessionChildren()
    {
        $sessionObjective = m::mock('AppBundle\Entity\Objective');
        $session = m::mock('AppBundle\Entity\Session');
        $this->object->addSession($session);
        $courseObjective = m::mock('AppBundle\Entity\Objective');
        $courseObjective->shouldReceive('addCourse')->with($this->object)->once();
        $courseObjective->shouldReceive('removeCourse')->with($this->object)->once();

        $session->shouldReceive('getObjectives')->andReturn([$sessionObjective])->once();
        $sessionObjective->shouldReceive('removeParent')->with($courseObjective)->once();
        $this->object->addObjective($courseObjective);
        $this->object->removeObjective($courseObjective);
    }

    /**
     * @covers \AppBundle\Entity\Course::addSequenceBlock
     */
    public function testAddSequenceBlock()
    {
        $this->entityCollectionAddTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \AppBundle\Entity\Course::removeSequenceBlock
     */
    public function testRemoveSequenceBlock()
    {
        $this->entityCollectionRemoveTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \AppBundle\Entity\Course::getSequenceBlocks
     * @covers \AppBundle\Entity\Course::setSequenceBlocks
     */
    public function testSetSequenceBlocks()
    {
        $this->entityCollectionSetTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }
}
