<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\CourseInterface;
use App\Entity\CourseLearningMaterialInterface;
use App\Entity\CourseObjectiveInterface;
use App\Entity\MeshDescriptor;
use App\Entity\SessionInterface;
use App\Entity\SessionLearningMaterialInterface;
use DateTime;
use Mockery as m;

/**
 * Tests for Entity MeshDescriptor
 * @group model
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Entity\MeshDescriptor::class)]
class MeshDescriptorTest extends EntityBase
{
    protected MeshDescriptor $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new MeshDescriptor();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'name',
        ];
        $this->validateNotBlanks($notBlank);

        $this->object->setId('');
        $this->object->setName('test name');
        $this->object->setAnnotation('');
        $this->validate(0);
        $this->object->setId('test');
        $this->object->setAnnotation('test');
        $this->validate(0);
    }
    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getCourses());
        $this->assertCount(0, $this->object->getCourseLearningMaterials());
        $this->assertCount(0, $this->object->getSessions());
        $this->assertCount(0, $this->object->getSessionLearningMaterials());
        $this->assertCount(0, $this->object->getTrees());
        $now = new DateTime();
        $createdAt = $this->object->getCreatedAt();
        $diff = $now->diff($createdAt);
        $this->assertTrue($diff->s < 2);
    }

    public function testSetName(): void
    {
        $this->basicSetTest('name', 'string');
    }

    public function testSetAnnotation(): void
    {
        $this->basicSetTest('annotation', 'string');
    }

    public function testAddCourse(): void
    {
        $this->entityCollectionAddTest('course', 'Course', false, false, 'addMeshDescriptor');
    }

    public function testRemoveCourse(): void
    {
        $this->entityCollectionRemoveTest('course', 'Course', false, false, false, 'removeMeshDescriptor');
    }

    public function testGetCourses(): void
    {
        $this->entityCollectionSetTest('course', 'Course', false, false, 'addMeshDescriptor');
    }

    public function testAddSession(): void
    {
        $this->entityCollectionAddTest('session', 'Session', false, false, 'addMeshDescriptor');
    }

    public function testRemoveSession(): void
    {
        $this->entityCollectionRemoveTest('session', 'Session', false, false, false, 'removeMeshDescriptor');
    }

    public function testGetSessions(): void
    {
        $this->entityCollectionSetTest('session', 'Session', false, false, 'addMeshDescriptor');
    }

    public function testAddConcept(): void
    {
        $this->entityCollectionAddTest('concept', 'MeshConcept', false, false, 'addDescriptor');
    }

    public function testRemoveConcept(): void
    {
        $this->entityCollectionRemoveTest('concept', 'MeshConcept', false, false, false, 'removeDescriptor');
    }

    public function testGetConcepts(): void
    {
        $this->entityCollectionSetTest('concept', 'MeshConcept', false, false, 'addDescriptor');
    }

    public function testAddQualifier(): void
    {
        $this->entityCollectionAddTest('qualifier', 'MeshQualifier', false, false, 'addDescriptor');
    }

    public function testRemoveQualifier(): void
    {
        $this->entityCollectionRemoveTest('qualifier', 'MeshQualifier', false, false, false, 'removeDescriptor');
    }

    public function testGetQualifiers(): void
    {
        $this->entityCollectionSetTest('qualifier', 'MeshQualifier', false, false, 'addDescriptor');
    }

    public function testAddTree(): void
    {
        $this->entityCollectionAddTest('tree', 'MeshTree');
    }

    public function testRemoveTree(): void
    {
        $this->entityCollectionRemoveTest('tree', 'MeshTree');
    }

    public function testGetTrees(): void
    {
        $this->entityCollectionSetTest('tree', 'MeshTree');
    }

    public function testAddSessionLearningMaterial(): void
    {
        $this->entityCollectionAddTest(
            'sessionLearningMaterial',
            'SessionLearningMaterial',
            false,
            false,
            'addMeshDescriptor'
        );
    }

    public function testRemoveSessionLearningMaterial(): void
    {
        $this->entityCollectionRemoveTest(
            'sessionLearningMaterial',
            'SessionLearningMaterial',
            false,
            false,
            false,
            'removeMeshDescriptor'
        );
    }

    public function testGetSessionLearningMaterials(): void
    {
        $this->entityCollectionSetTest(
            'sessionLearningMaterial',
            'SessionLearningMaterial',
            false,
            false,
            'addMeshDescriptor'
        );
    }

    public function testAddCourseLearningMaterial(): void
    {
        $this->entityCollectionAddTest(
            'courseLearningMaterial',
            'CourseLearningMaterial',
            false,
            false,
            'addMeshDescriptor'
        );
    }

    public function testRemoveCourseLearningMaterial(): void
    {
        $this->entityCollectionRemoveTest(
            'courseLearningMaterial',
            'CourseLearningMaterial',
            false,
            false,
            false,
            'removeMeshDescriptor'
        );
    }

    public function testGetCourseLearningMaterials(): void
    {
        $this->entityCollectionSetTest(
            'courseLearningMaterial',
            'CourseLearningMaterial',
            false,
            false,
            'addMeshDescriptor'
        );
    }

    public function testSetPermuted(): void
    {
        $this->booleanSetTest('deleted');
    }

    public function testGetIndexableCoursesFromObjectives(): void
    {
        $course1 = m::mock(CourseInterface::class);
        $objective1 = m::mock(CourseObjectiveInterface::class);
        $objective1
            ->shouldReceive('getIndexableCourses')->once()
            ->andReturn([$course1]);
        $course2 = m::mock(CourseInterface::class);
        $objective2 = m::mock(CourseObjectiveInterface::class);
        $objective2
            ->shouldReceive('getIndexableCourses')->once()
            ->andReturn([$course2]);
        $this->object->addCourseObjective($objective1);
        $this->object->addCourseObjective($objective2);

        $rhett = $this->object->getIndexableCourses();
        $this->assertEquals([$course1, $course2], $rhett);
    }

    public function testGetIndexableCoursesForLearningMaterials(): void
    {
        $course1 = m::mock(CourseInterface::class);
        $courseLearningMaterial = m::mock(CourseLearningMaterialInterface::class);
        $courseLearningMaterial->shouldReceive('addMeshDescriptor')->once();
        $courseLearningMaterial->shouldReceive('getCourse')->once()->andReturn($course1);
        $this->object->addCourseLearningMaterial($courseLearningMaterial);

        $course2 = m::mock(CourseInterface::class);
        $session = m::mock(SessionInterface::class);
        $session->shouldReceive('getCourse')->once()->andReturn($course2);
        $sessionLearningMaterial = m::mock(SessionLearningMaterialInterface::class);
        $sessionLearningMaterial->shouldReceive('addMeshDescriptor')->once();
        $sessionLearningMaterial->shouldReceive('getSession')->once()->andReturn($session);
        $this->object->addSessionLearningMaterial($sessionLearningMaterial);

        $rhett = $this->object->getIndexableCourses();
        $this->assertEquals([$course1, $course2], $rhett);
    }

    public function testGetIndexableCoursesForCoursesAndSessions(): void
    {
        $course1 = m::mock(CourseInterface::class);
        $course1->shouldReceive('addMeshDescriptor')->once()->with($this->object);
        $this->object->addCourse($course1);

        $course2 = m::mock(CourseInterface::class);
        $session = m::mock(SessionInterface::class);
        $session->shouldReceive('addMeshDescriptor')->once()->with($this->object);
        $session->shouldReceive('getCourse')->once()->andReturn($course2);
        $this->object->addSession($session);

        $rhett = $this->object->getIndexableCourses();
        $this->assertEquals($rhett, [$course1, $course2]);
    }

    public function testAddProgramYearObjective(): void
    {
        $this->entityCollectionAddTest('programYearObjective', 'ProgramYearObjective');
    }

    public function testRemoveProgramYearObjective(): void
    {
        $this->entityCollectionRemoveTest('programYearObjective', 'ProgramYearObjective');
    }

    public function testGetProgramYearObjectives(): void
    {
        $this->entityCollectionSetTest('programYearObjective', 'ProgramYearObjective');
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

    public function testAddSessionObjective(): void
    {
        $this->entityCollectionAddTest('sessionObjective', 'SessionObjective');
    }

    public function testRemoveSessionObjective(): void
    {
        $this->entityCollectionRemoveTest('sessionObjective', 'SessionObjective');
    }

    public function testGetSessionObjectives(): void
    {
        $this->entityCollectionSetTest('sessionObjective', 'SessionObjective');
    }

    protected function getObject(): MeshDescriptor
    {
        return $this->object;
    }
}
