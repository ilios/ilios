<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Objective;
use Mockery as m;

/**
 * Tests for Entity Objective
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
     * @covers \AppBundle\Entity\Course::__construct
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
     * @covers \AppBundle\Entity\Objective::setTitle
     * @covers \AppBundle\Entity\Objective::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \AppBundle\Entity\Objective::setCompetency
     * @covers \AppBundle\Entity\Objective::getCompetency
     */
    public function testSetCompetency()
    {
        $this->entitySetTest('competency', 'Competency');
    }

    /**
     * @covers \AppBundle\Entity\Objective::addCourse
     */
    public function testAddCourse()
    {
        $this->entityCollectionAddTest('course', 'Course', false, false, 'addObjective');
    }

    /**
     * @covers \AppBundle\Entity\Objective::removeCourse
     */
    public function testRemoveCourse()
    {
        $this->entityCollectionRemoveTest('course', 'Course', false, false, false, 'removeObjective');
    }

    /**
     * @covers \AppBundle\Entity\Objective::getCourses
     */
    public function testGetCourses()
    {
        $this->entityCollectionSetTest('course', 'Course', false, false, 'addObjective');
    }

    /**
     * @covers \AppBundle\Entity\Objective::addProgramYear
     */
    public function testAddProgramYear()
    {
        $this->entityCollectionAddTest('programYear', 'ProgramYear', false, false, 'addObjective');
    }

    /**
     * @covers \AppBundle\Entity\Objective::removeProgramYear
     */
    public function testRemoveProgramYear()
    {
        $this->entityCollectionRemoveTest('programYear', 'ProgramYear', false, false, false, 'removeObjective');
    }

    /**
     * @covers \AppBundle\Entity\Objective::getProgramYears
     */
    public function testGetProgramYears()
    {
        $this->entityCollectionSetTest('programYear', 'ProgramYear', false, false, 'addObjective');
    }

    /**
     * @covers \AppBundle\Entity\Objective::addSession
     */
    public function testAddSession()
    {
        $this->entityCollectionAddTest('session', 'Session', false, false, 'addObjective');
    }

    /**
     * @covers \AppBundle\Entity\Objective::removeSession
     */
    public function testRemoveSession()
    {
        $this->entityCollectionRemoveTest('session', 'Session', false, false, false, 'removeObjective');
    }

    /**
     * @covers \AppBundle\Entity\Objective::getSessions
     */
    public function testGetSessions()
    {
        $this->entityCollectionSetTest('session', 'Session', false, false, 'addObjective');
    }

    /**
     * @covers \AppBundle\Entity\Objective::addChild
     */
    public function testAddChild()
    {
        $this->entityCollectionAddTest('children', 'Objective', 'getChildren', 'addChild', 'addParent');
    }

    /**
     * @covers \AppBundle\Entity\Objective::removeChild
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
     * @covers \AppBundle\Entity\Objective::getChildren
     * @covers \AppBundle\Entity\Objective::setChildren
     */
    public function testGetChildren()
    {
        $this->entityCollectionSetTest('children', 'Objective', 'getChildren', 'setChildren', 'addParent');
    }

    /**
     * @covers \AppBundle\Entity\Objective::addMeshDescriptor
     */
    public function testAddMeshDescriptor()
    {
        $this->entityCollectionAddTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \AppBundle\Entity\Objective::removeMeshDescriptor
     */
    public function testRemoveMeshDescriptor()
    {
        $this->entityCollectionRemoveTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \AppBundle\Entity\Objective::getMeshDescriptors
     */
    public function testGetMeshDescriptors()
    {
        $this->entityCollectionSetTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \AppBundle\Entity\Objective::addParent
     */
    public function testAddParent()
    {
        $this->entityCollectionAddTest('parent', 'Objective');
    }

    /**
     * @covers \AppBundle\Entity\Objective::removeParent
     */
    public function testRemoveParent()
    {
        $this->entityCollectionRemoveTest('parent', 'Objective');
    }

    /**
     * @covers \AppBundle\Entity\Objective::getParents
     * @covers \AppBundle\Entity\Objective::setParents
     */
    public function testGetParents()
    {
        $this->entityCollectionSetTest('parent', 'Objective');
    }

    /**
     * @covers \AppBundle\Entity\Objective::setAncestor
     * @covers \AppBundle\Entity\Objective::getAncestor
     */
    public function testSetAncestor()
    {
        $this->entitySetTest('ancestor', 'Objective');
    }

    /**
     * @covers \AppBundle\Entity\Objective::getAncestorOrSelf
     */
    public function testGetAncestorOrSelfWithAncestor()
    {
        $ancestor = m::mock('AppBundle\Entity\Objective');
        $this->object->setAncestor($ancestor);
        $this->assertSame($ancestor, $this->object->getAncestorOrSelf());
    }

    /**
     * @covers \AppBundle\Entity\Objective::getAncestorOrSelf
     */
    public function testGetAncestorOrSelfWithNoAncestor()
    {
        $this->assertSame($this->object, $this->object->getAncestorOrSelf());
    }

    /**
     * @covers \AppBundle\Entity\Objective::addDescendant
     */
    public function testAddDescendant()
    {
        $this->entityCollectionAddTest('descendant', 'Objective');
    }

    /**
     * @covers \AppBundle\Entity\Objective::removeDescendant
     */
    public function testRemoveDescendant()
    {
        $this->entityCollectionRemoveTest('descendant', 'Objective');
    }

    /**
     * @covers \AppBundle\Entity\Objective::getDescendants
     * @covers \AppBundle\Entity\Objective::setDescendants
     */
    public function testGetDescendants()
    {
        $this->entityCollectionSetTest('descendant', 'Objective');
    }

    /**
     * @covers \AppBundle\Entity\Objective::setPosition
     * @covers \AppBundle\Entity\Objective::getPosition
     */
    public function testSetPosition()
    {
        $this->basicSetTest('position', 'integer');
    }
}
