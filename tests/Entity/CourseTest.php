<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Entity\Course;
use App\Entity\CourseObjectiveInterface;
use App\Entity\School;
use App\Entity\SessionInterface;
use App\Entity\SessionObjectiveInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;

/**
 * Tests for Entity Course
 */
#[Group('model')]
#[CoversClass(Course::class)]
final class CourseTest extends EntityBase
{
    protected Course $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new Course();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'title',
            'level',
            'year',
            'startDate',
            'endDate',
        ];
        $this->object->setSchool(new School());
        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('test');
        $this->object->setLevel(3);
        $this->object->setYear(2004);
        $this->object->setStartDate(new DateTime());
        $this->object->setEndDate(new DateTime());
        $this->object->setExternalId('');
        $this->validate(0);
        $this->object->setExternalId('test');
        $this->validate(0);
    }

    public function testNotNullValidation(): void
    {
        $notNull = [
            'school',
        ];
        $this->object->setTitle('test');
        $this->object->setLevel(3);
        $this->object->setYear(2004);
        $this->object->setStartDate(new DateTime());
        $this->object->setEndDate(new DateTime());
        $this->validateNotNulls($notNull);

        $this->object->setSchool(new School());
        $this->validate(0);
    }

    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getCohorts());
        $this->assertCount(0, $this->object->getDirectors());
        $this->assertCount(0, $this->object->getMeshDescriptors());
        $this->assertCount(0, $this->object->getCourseObjectives());
        $this->assertCount(0, $this->object->getLearningMaterials());
        $this->assertCount(0, $this->object->getSessions());
        $this->assertCount(0, $this->object->getTerms());
        $this->assertCount(0, $this->object->getDescendants());
        $this->assertCount(0, $this->object->getAdministrators());
        $this->assertCount(0, $this->object->getStudentAdvisors());
    }

    public function testSetTitle(): void
    {
        $this->basicSetTest('title', 'string');
    }

    public function testSetCourseLevel(): void
    {
        $this->basicSetTest('level', 'integer');
    }

    public function testSetYear(): void
    {
        $this->basicSetTest('year', 'integer');
    }

    public function testSetStartDate(): void
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    public function testSetEndDate(): void
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    public function testSetExternalId(): void
    {
        $this->basicSetTest('externalId', 'string');
    }

    public function testSetLocked(): void
    {
        $this->booleanSetTest('locked');
    }

    public function testSetArchived(): void
    {
        $this->booleanSetTest('archived');
    }

    public function testSetPublishedAsTbd(): void
    {
        $this->booleanSetTest('publishedAsTbd');
    }

    public function testSetPublished(): void
    {
        $this->booleanSetTest('published');
    }

    public function testSetSchool(): void
    {
        $this->entitySetTest('school', 'School');
    }

    public function testSetClerkshipType(): void
    {
         $this->entitySetTest('clerkshipType', 'CourseClerkshipType');
    }

    public function testAddDirector(): void
    {
        $this->entityCollectionAddTest('director', 'User', false, false, 'addDirectedCourse');
    }

    public function testRemoveDirector(): void
    {
        $this->entityCollectionRemoveTest('director', 'User', false, false, false, 'removeDirectedCourse');
    }

    public function testGetDirectors(): void
    {
        $this->entityCollectionSetTest('director', 'User', false, false, 'addDirectedCourse');
    }

    public function testAddCohort(): void
    {
        $this->entityCollectionAddTest('cohort', 'Cohort', false, false, 'addCourse');
    }

    public function testRemoveCohort(): void
    {
        $this->entityCollectionRemoveTest('cohort', 'Cohort', false, false, false, 'removeCourse');
    }

    public function testGetCohorts(): void
    {
        $this->entityCollectionSetTest('cohort', 'Cohort', false, false, 'addCourse');
    }

    public function testAddLearningMaterial(): void
    {
        $this->entityCollectionAddTest('learningMaterial', 'CourseLearningMaterial');
    }

    public function testRemoveLearningMaterial(): void
    {
        $this->entityCollectionRemoveTest('learningMaterial', 'CourseLearningMaterial');
    }

    public function testGetLearningMaterials(): void
    {
        $this->entityCollectionSetTest('learningMaterial', 'CourseLearningMaterial');
    }

    public function testAddTerm(): void
    {
        $this->entityCollectionAddTest('term', 'Term', false, false, 'addCourse');
    }

    public function testRemoveTerm(): void
    {
        $this->entityCollectionRemoveTest('term', 'Term', false, false, false, 'removeCourse');
    }

    public function testSetTerms(): void
    {
        $this->entityCollectionSetTest('term', 'Term', false, false, 'addCourse');
    }

    public function testSetAncestor(): void
    {
        $this->entitySetTest('ancestor', 'Course');
    }

    public function testGetAncestorOrSelfWithAncestor(): void
    {
        $ancestor = m::mock(Course::class);
        $this->object->setAncestor($ancestor);
        $this->assertSame($ancestor, $this->object->getAncestorOrSelf());
    }

    public function testGetAncestorOrSelfWithNoAncestor(): void
    {
        $this->assertSame($this->object, $this->object->getAncestorOrSelf());
    }

    public function testAddDescendant(): void
    {
        $this->entityCollectionAddTest('descendant', 'Course');
    }

    public function testRemoveDescendant(): void
    {
        $this->entityCollectionRemoveTest('descendant', 'Course');
    }

    public function testGetDescendants(): void
    {
        $this->entityCollectionSetTest('descendant', 'Course');
    }

    public function testAddSession(): void
    {
        $this->entityCollectionAddTest('session', 'Session');
    }

    public function testRemoveSession(): void
    {
        $this->entityCollectionRemoveTest('session', 'Session');
    }

    public function testGetSessions(): void
    {
        $this->entityCollectionSetTest('session', 'Session');
    }

    public function testAddAdministrator(): void
    {
        $this->entityCollectionAddTest('administrator', 'User', false, false, 'addAdministeredCourse');
    }

    public function testRemoveAdministrator(): void
    {
        $this->entityCollectionRemoveTest('administrator', 'User', false, false, false, 'removeAdministeredCourse');
    }

    public function testSetAdministrators(): void
    {
        $this->entityCollectionSetTest('administrator', 'User', false, false, 'addAdministeredCourse');
    }

    public function testAddStudentAdvisor(): void
    {
        $this->entityCollectionAddTest('studentAdvisor', 'User', false, false, 'addStudentAdvisedCourse');
    }

    public function testRemoveStudentAdvisor(): void
    {
        $this->entityCollectionRemoveTest('studentAdvisor', 'User', false, false, false, 'removeStudentAdvisedCourse');
    }

    public function testSetStudentAdvisors(): void
    {
        $this->entityCollectionSetTest('studentAdvisor', 'User', false, false, 'addStudentAdvisedCourse');
    }

    public function testRemoveObjectiveWithSessionChildren(): void
    {
        $courseObjective = m::mock(CourseObjectiveInterface::class);
        $sessionObjective = m::mock(SessionObjectiveInterface::class);
        $sessionObjective->shouldReceive('removeCourseObjective')->with($courseObjective)->once();
        $session = m::mock(SessionInterface::class);
        $session->shouldReceive('getSessionObjectives')
            ->andReturn(new ArrayCollection([$sessionObjective]))->once();
        $this->object->addSession($session);
        $this->object->addCourseObjective($courseObjective);
        $this->object->removeCourseObjective($courseObjective);
    }

    public function testAddCourseObjective(): void
    {
        $this->entityCollectionAddTest('courseObjective', 'CourseObjective');
    }

    public function testRemoveCourseObjective(): void
    {
        $this->entityCollectionRemoveTest('courseObjective', 'CourseObjective');
    }

    public function testGetCourseObjectives(): void
    {
        $this->entityCollectionSetTest('courseObjective', 'CourseObjective');
    }

    public function testAddSequenceBlock(): void
    {
        $this->entityCollectionAddTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    public function testRemoveSequenceBlock(): void
    {
        $this->entityCollectionRemoveTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    public function testSetSequenceBlocks(): void
    {
        $this->entityCollectionSetTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    public function testGetIndexableCourses(): void
    {
        $this->assertEquals([$this->object], $this->object->getIndexableCourses());
    }

    protected function getObject(): Course
    {
        return $this->object;
    }
}
