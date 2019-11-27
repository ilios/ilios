<?php
namespace App\Tests\Entity;

use App\Entity\CourseInterface;
use App\Entity\Objective;
use App\Entity\SessionInterface;
use Mockery as m;

/**
 * Tests for Entity Objective
 * @group model
 */
class ObjectiveTest extends EntityBase
{
    /**
     * @var Objective
     */
    protected $object;

    /**
     * Instantiate a Objective object
     */
    protected function setUp()
    {
        $this->object = new Objective;
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'title'
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('test');
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\Course::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getMeshDescriptors());
        $this->assertEmpty($this->object->getSessions());
        $this->assertEmpty($this->object->getCourses());
        $this->assertEmpty($this->object->getProgramYears());
        $this->assertEmpty($this->object->getDescendants());
        $this->assertEmpty($this->object->getParents());
        $this->assertEmpty($this->object->getChildren());
        $this->assertEmpty($this->object->getDescendants());
    }

    /**
     * @covers \App\Entity\Objective::setTitle
     * @covers \App\Entity\Objective::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\Objective::setCompetency
     * @covers \App\Entity\Objective::getCompetency
     */
    public function testSetCompetency()
    {
        $this->entitySetTest('competency', 'Competency');
    }

    /**
     * @covers \App\Entity\Objective::addCourse
     */
    public function testAddCourse()
    {
        $this->entityCollectionAddTest('course', 'Course', false, false, 'addObjective');
    }

    /**
     * @covers \App\Entity\Objective::removeCourse
     */
    public function testRemoveCourse()
    {
        $this->entityCollectionRemoveTest('course', 'Course', false, false, false, 'removeObjective');
    }

    /**
     * @covers \App\Entity\Objective::getCourses
     */
    public function testGetCourses()
    {
        $this->entityCollectionSetTest('course', 'Course', false, false, 'addObjective');
    }

    /**
     * @covers \App\Entity\Objective::addProgramYear
     */
    public function testAddProgramYear()
    {
        $this->entityCollectionAddTest('programYear', 'ProgramYear', false, false, 'addObjective');
    }

    /**
     * @covers \App\Entity\Objective::removeProgramYear
     */
    public function testRemoveProgramYear()
    {
        $this->entityCollectionRemoveTest('programYear', 'ProgramYear', false, false, false, 'removeObjective');
    }

    /**
     * @covers \App\Entity\Objective::getProgramYears
     */
    public function testGetProgramYears()
    {
        $this->entityCollectionSetTest('programYear', 'ProgramYear', false, false, 'addObjective');
    }

    /**
     * @covers \App\Entity\Objective::addSession
     */
    public function testAddSession()
    {
        $this->entityCollectionAddTest('session', 'Session', false, false, 'addObjective');
    }

    /**
     * @covers \App\Entity\Objective::removeSession
     */
    public function testRemoveSession()
    {
        $this->entityCollectionRemoveTest('session', 'Session', false, false, false, 'removeObjective');
    }

    /**
     * @covers \App\Entity\Objective::getSessions
     */
    public function testGetSessions()
    {
        $this->entityCollectionSetTest('session', 'Session', false, false, 'addObjective');
    }

    /**
     * @covers \App\Entity\Objective::addChild
     */
    public function testAddChild()
    {
        $this->entityCollectionAddTest('children', 'Objective', 'getChildren', 'addChild', 'addParent');
    }

    /**
     * @covers \App\Entity\Objective::removeChild
     */
    public function testRemoveChild()
    {
        $this->entityCollectionRemoveTest(
            'children',
            'Objective',
            'getChildren',
            'addChild',
            'removeChild',
            'removeParent'
        );
    }

    /**
     * @covers \App\Entity\Objective::getChildren
     * @covers \App\Entity\Objective::setChildren
     */
    public function testGetChildren()
    {
        $this->entityCollectionSetTest('children', 'Objective', 'getChildren', 'setChildren', 'addParent');
    }

    /**
     * @covers \App\Entity\Objective::addMeshDescriptor
     */
    public function testAddMeshDescriptor()
    {
        $this->entityCollectionAddTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \App\Entity\Objective::removeMeshDescriptor
     */
    public function testRemoveMeshDescriptor()
    {
        $this->entityCollectionRemoveTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \App\Entity\Objective::getMeshDescriptors
     */
    public function testGetMeshDescriptors()
    {
        $this->entityCollectionSetTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \App\Entity\Objective::addParent
     */
    public function testAddParent()
    {
        $this->entityCollectionAddTest('parent', 'Objective');
    }

    /**
     * @covers \App\Entity\Objective::removeParent
     */
    public function testRemoveParent()
    {
        $this->entityCollectionRemoveTest('parent', 'Objective');
    }

    /**
     * @covers \App\Entity\Objective::getParents
     * @covers \App\Entity\Objective::setParents
     */
    public function testGetParents()
    {
        $this->entityCollectionSetTest('parent', 'Objective');
    }

    /**
     * @covers \App\Entity\Objective::setAncestor
     * @covers \App\Entity\Objective::getAncestor
     */
    public function testSetAncestor()
    {
        $this->entitySetTest('ancestor', 'Objective');
    }

    /**
     * @covers \App\Entity\Objective::getAncestorOrSelf
     */
    public function testGetAncestorOrSelfWithAncestor()
    {
        $ancestor = m::mock('App\Entity\Objective');
        $this->object->setAncestor($ancestor);
        $this->assertSame($ancestor, $this->object->getAncestorOrSelf());
    }

    /**
     * @covers \App\Entity\Objective::getAncestorOrSelf
     */
    public function testGetAncestorOrSelfWithNoAncestor()
    {
        $this->assertSame($this->object, $this->object->getAncestorOrSelf());
    }

    /**
     * @covers \App\Entity\Objective::addDescendant
     */
    public function testAddDescendant()
    {
        $this->entityCollectionAddTest('descendant', 'Objective');
    }

    /**
     * @covers \App\Entity\Objective::removeDescendant
     */
    public function testRemoveDescendant()
    {
        $this->entityCollectionRemoveTest('descendant', 'Objective');
    }

    /**
     * @covers \App\Entity\Objective::getDescendants
     * @covers \App\Entity\Objective::setDescendants
     */
    public function testGetDescendants()
    {
        $this->entityCollectionSetTest('descendant', 'Objective');
    }

    /**
     * @covers \App\Entity\Objective::setPosition
     * @covers \App\Entity\Objective::getPosition
     */
    public function testSetPosition()
    {
        $this->basicSetTest('position', 'integer');
    }

    /**
     * @covers \App\Entity\Objective::setActive
     * @covers \App\Entity\Objective::isActive
     */
    public function testSetActive()
    {
        $this->booleanSetTest('active');
    }

    /**
     * @covers \App\Entity\Objective::getIndexableCourses
     */
    public function testGetIndexableCourses()
    {
        $course1 = m::mock(CourseInterface::class)
            ->shouldReceive('addObjective')->once()->with($this->object)->getMock();
        $this->object->addCourse($course1);

        $course2 = m::mock(CourseInterface::class);
        $session = m::mock(SessionInterface::class)
            ->shouldReceive('addObjective')->once()->with($this->object)
            ->shouldReceive('getCourse')->once()
            ->andReturn($course2);
        $this->object->addSession($session->getMock());

        $rhett = $this->object->getIndexableCourses();
        $this->assertEquals($rhett, [$course1, $course2]);
    }
}
