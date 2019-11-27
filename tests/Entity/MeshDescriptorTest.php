<?php
namespace App\Tests\Entity;

use App\Entity\CourseInterface;
use App\Entity\CourseLearningMaterialInterface;
use App\Entity\MeshDescriptor;
use App\Entity\ObjectiveInterface;
use App\Entity\SessionInterface;
use App\Entity\SessionLearningMaterialInterface;
use DateTime;
use Mockery as m;

/**
 * Tests for Entity MeshDescriptor
 * @group model
 */
class MeshDescriptorTest extends EntityBase
{
    /**
     * @var MeshDescriptor
     */
    protected $object;

    /**
     * Instantiate a MeshDescriptor object
     */
    protected function setUp()
    {
        $this->object = new MeshDescriptor;
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'name'
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setName('test name');
        $this->validate(0);
    }
    /**
     * @covers \App\Entity\MeshDescriptor::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getCourses());
        $this->assertEmpty($this->object->getCourseLearningMaterials());
        $this->assertEmpty($this->object->getObjectives());
        $this->assertEmpty($this->object->getSessions());
        $this->assertEmpty($this->object->getSessionLearningMaterials());
        $this->assertEmpty($this->object->getTrees());
        $now = DateTime::createFromFormat('U', time());
        $createdAt = $this->object->getCreatedAt();
        $this->assertTrue($createdAt instanceof DateTime);
        $diff = $now->diff($createdAt);
        $this->assertTrue($diff->s < 2);
    }

    /**
     * @covers \App\Entity\MeshDescriptor::setName
     * @covers \App\Entity\MeshDescriptor::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \App\Entity\MeshDescriptor::setAnnotation
     * @covers \App\Entity\MeshDescriptor::getAnnotation
     */
    public function testSetAnnotation()
    {
        $this->basicSetTest('annotation', 'string');
    }

    /**
     * @covers \App\Entity\MeshDescriptor::addCourse
     */
    public function testAddCourse()
    {
        $this->entityCollectionAddTest('course', 'Course', false, false, 'addMeshDescriptor');
    }

    /**
     * @covers \App\Entity\MeshDescriptor::removeCourse
     */
    public function testRemoveCourse()
    {
        $this->entityCollectionRemoveTest('course', 'Course', false, false, false, 'removeMeshDescriptor');
    }

    /**
     * @covers \App\Entity\MeshDescriptor::getCourses
     */
    public function testGetCourses()
    {
        $this->entityCollectionSetTest('course', 'Course', false, false, 'addMeshDescriptor');
    }

    /**
     * @covers \App\Entity\MeshDescriptor::addObjective
     */
    public function testAddObjective()
    {
        $this->entityCollectionAddTest('objective', 'Objective', false, false, 'addMeshDescriptor');
    }

    /**
     * @covers \App\Entity\MeshDescriptor::removeObjective
     */
    public function testRemoveObjective()
    {
        $this->entityCollectionRemoveTest('objective', 'Objective', false, false, false, 'removeMeshDescriptor');
    }

    /**
     * @covers \App\Entity\MeshDescriptor::setObjectives
     * @covers \App\Entity\MeshDescriptor::getObjectives
     */
    public function testGetObjectives()
    {
        $this->entityCollectionSetTest('objective', 'Objective', false, false, 'addMeshDescriptor');
    }

    /**
     * @covers \App\Entity\MeshDescriptor::addSession
     */
    public function testAddSession()
    {
        $this->entityCollectionAddTest('session', 'Session', false, false, 'addMeshDescriptor');
    }

    /**
     * @covers \App\Entity\MeshDescriptor::removeSession
     */
    public function testRemoveSession()
    {
        $this->entityCollectionRemoveTest('session', 'Session', false, false, false, 'removeMeshDescriptor');
    }

    /**
     * @covers \App\Entity\MeshDescriptor::getSessions
     */
    public function testGetSessions()
    {
        $this->entityCollectionSetTest('session', 'Session', false, false, 'addMeshDescriptor');
    }

    /**
     * @covers \App\Entity\MeshDescriptor::addConcept
     */
    public function testAddConcept()
    {
        $this->entityCollectionAddTest('concept', 'MeshConcept', false, false, 'addDescriptor');
    }

    /**
     * @covers \App\Entity\MeshDescriptor::removeConcept
     */
    public function testRemoveConcept()
    {
        $this->entityCollectionRemoveTest('concept', 'MeshConcept', false, false, false, 'removeDescriptor');
    }

    /**
     * @covers \App\Entity\MeshDescriptor::getConcepts
     */
    public function testGetConcepts()
    {
        $this->entityCollectionSetTest('concept', 'MeshConcept', false, false, 'addDescriptor');
    }

    /**
     * @covers \App\Entity\MeshDescriptor::addQualifier
     */
    public function testAddQualifier()
    {
        $this->entityCollectionAddTest('qualifier', 'MeshQualifier', false, false, 'addDescriptor');
    }

    /**
     * @covers \App\Entity\MeshDescriptor::removeQualifier
     */
    public function testRemoveQualifier()
    {
        $this->entityCollectionRemoveTest('qualifier', 'MeshQualifier', false, false, false, 'removeDescriptor');
    }

    /**
     * @covers \App\Entity\MeshDescriptor::getQualifiers
     * @covers \App\Entity\MeshDescriptor::setQualifiers
     */
    public function testGetQualifiers()
    {
        $this->entityCollectionSetTest('qualifier', 'MeshQualifier', false, false, 'addDescriptor');
    }

    /**
     * @covers \App\Entity\MeshDescriptor::addTree
     */
    public function testAddTree()
    {
        $this->entityCollectionAddTest('tree', 'MeshTree');
    }

    /**
     * @covers \App\Entity\MeshDescriptor::removeTree
     */
    public function testRemoveTree()
    {
        $this->entityCollectionRemoveTest('tree', 'MeshTree');
    }

    /**
     * @covers \App\Entity\MeshDescriptor::getTrees
     * @covers \App\Entity\MeshDescriptor::setTrees
     */
    public function testGetTrees()
    {
        $this->entityCollectionSetTest('tree', 'MeshTree');
    }

    /**
     * @covers \App\Entity\MeshDescriptor::addSessionLearningMaterial
     */
    public function testAddSessionLearningMaterial()
    {
        $this->entityCollectionAddTest(
            'sessionLearningMaterial',
            'SessionLearningMaterial',
            false,
            false,
            'addMeshDescriptor'
        );
    }

    /**
     * @covers \App\Entity\MeshDescriptor::removeSessionLearningMaterial
     */
    public function testRemoveSessionLearningMaterial()
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

    /**
     * @covers \App\Entity\MeshDescriptor::getSessionLearningMaterials
     * @covers \App\Entity\MeshDescriptor::setSessionLearningMaterials
     */
    public function testGetSessionLearningMaterials()
    {
        $this->entityCollectionSetTest(
            'sessionLearningMaterial',
            'SessionLearningMaterial',
            false,
            false,
            'addMeshDescriptor'
        );
    }

    /**
     * @covers \App\Entity\MeshDescriptor::addCourseLearningMaterial
     */
    public function testAddCourseLearningMaterial()
    {
        $this->entityCollectionAddTest(
            'courseLearningMaterial',
            'CourseLearningMaterial',
            false,
            false,
            'addMeshDescriptor'
        );
    }

    /**
     * @covers \App\Entity\MeshDescriptor::removeCourseLearningMaterial
     */
    public function testRemoveCourseLearningMaterial()
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

    /**
     * @covers \App\Entity\MeshDescriptor::getCourseLearningMaterials
     * @covers \App\Entity\MeshDescriptor::setCourseLearningMaterials
     */
    public function testGetCourseLearningMaterials()
    {
        $this->entityCollectionSetTest(
            'courseLearningMaterial',
            'CourseLearningMaterial',
            false,
            false,
            'addMeshDescriptor'
        );
    }

    /**
     * @covers \App\Entity\MeshDescriptor::setDeleted
     * @covers \App\Entity\MeshDescriptor::isDeleted()
     */
    public function testSetPermuted()
    {
        $this->booleanSetTest('deleted');
    }

    /**
     * @covers \App\Entity\MeshDescriptor::getIndexableCourses
     */
    public function testGetIndexableCoursesFromObjectives()
    {
        $course1 = m::mock(CourseInterface::class);
        $objective1 = m::mock(ObjectiveInterface::class)
            ->shouldReceive('getIndexableCourses')->once()
            ->andReturn([$course1])
            ->shouldReceive('addMeshDescriptor')->once()->getMock();
        $course2 = m::mock(CourseInterface::class);
        $objective2 = m::mock(ObjectiveInterface::class)
            ->shouldReceive('getIndexableCourses')->once()
            ->andReturn([$course2])
            ->shouldReceive('addMeshDescriptor')->once()->getMock();
        $this->object->addObjective($objective1);
        $this->object->addObjective($objective2);

        $rhett = $this->object->getIndexableCourses();
        $this->assertEquals([$course1, $course2], $rhett);
    }

    /**
     * @covers \App\Entity\MeshDescriptor::getIndexableCourses
     */
    public function testGetIndexableCoursesForLearningMaterials()
    {
        $course1 = m::mock(CourseInterface::class);
        $courseLearningMaterial = m::mock(CourseLearningMaterialInterface::class)
            ->shouldReceive('addMeshDescriptor')->once()
            ->shouldReceive('getCourse')->once()
            ->andReturn($course1);
        $this->object->addCourseLearningMaterial($courseLearningMaterial->getMock());

        $course2 = m::mock(CourseInterface::class);
        $session = m::mock(SessionInterface::class)
            ->shouldReceive('getCourse')->once()
            ->andReturn($course2);
        $sessionLearningMaterial = m::mock(SessionLearningMaterialInterface::class)
            ->shouldReceive('addMeshDescriptor')->once()
            ->shouldReceive('getSession')->once()
            ->andReturn($session->getMock());
        $this->object->addSessionLearningMaterial($sessionLearningMaterial->getMock());

        $rhett = $this->object->getIndexableCourses();
        $this->assertEquals([$course1, $course2], $rhett);
    }

    /**
     * @covers \App\Entity\MeshDescriptor::getIndexableCourses
     */
    public function testGetIndexableCoursesForCoursesAndSessions()
    {
        $course1 = m::mock(CourseInterface::class)
            ->shouldReceive('addMeshDescriptor')->once()->with($this->object)->getMock();
        $this->object->addCourse($course1);

        $course2 = m::mock(CourseInterface::class);
        $session = m::mock(SessionInterface::class)
            ->shouldReceive('addMeshDescriptor')->once()->with($this->object)
            ->shouldReceive('getCourse')->once()
            ->andReturn($course2);
        $this->object->addSession($session->getMock());

        $rhett = $this->object->getIndexableCourses();
        $this->assertEquals($rhett, [$course1, $course2]);
    }
}
