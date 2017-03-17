<?php
namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\Objective;
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
     * @covers \Ilios\CoreBundle\Entity\Course::__construct
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
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Objective::setTitle
     * @covers \Ilios\CoreBundle\Entity\Objective::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Objective::setCompetency
     * @covers \Ilios\CoreBundle\Entity\Objective::getCompetency
     */
    public function testSetCompetency()
    {
        $this->entitySetTest('competency', 'Competency');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Objective::addCourse
     */
    public function testAddCourse()
    {
        $this->entityCollectionAddTest('course', 'Course', false, false, 'addObjective');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Objective::removeCourse
     */
    public function testRemoveCourse()
    {
        $this->entityCollectionRemoveTest('course', 'Course', false, false, false, 'removeObjective');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Objective::getCourses
     */
    public function testGetCourses()
    {
        $this->entityCollectionSetTest('course', 'Course', false, false, 'addObjective');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Objective::addProgramYear
     */
    public function testAddProgramYear()
    {
        $this->entityCollectionAddTest('programYear', 'ProgramYear', false, false, 'addObjective');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Objective::removeProgramYear
     */
    public function testRemoveProgramYear()
    {
        $this->entityCollectionRemoveTest('programYear', 'ProgramYear', false, false, false, 'removeObjective');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Objective::getProgramYears
     */
    public function testGetProgramYears()
    {
        $this->entityCollectionSetTest('programYear', 'ProgramYear', false, false, 'addObjective');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Objective::addSession
     */
    public function testAddSession()
    {
        $this->entityCollectionAddTest('session', 'Session', false, false, 'addObjective');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Objective::removeSession
     */
    public function testRemoveSession()
    {
        $this->entityCollectionRemoveTest('session', 'Session', false, false, false, 'removeObjective');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Objective::getSessions
     */
    public function testGetSessions()
    {
        $this->entityCollectionSetTest('session', 'Session', false, false, 'addObjective');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Objective::addChild
     */
    public function testAddChild()
    {
        $this->entityCollectionAddTest('children', 'Objective', 'getChildren', 'addChild');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Objective::removeChild
     */
    public function testRemoveChild()
    {
        $this->entityCollectionRemoveTest('children', 'Objective', 'getChildren', 'addChild', 'removeChild');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Objective::getChildren
     * @covers \Ilios\CoreBundle\Entity\Objective::setChildren
     */
    public function testGetChildren()
    {
        $this->entityCollectionSetTest('children', 'Objective', 'getChildren', 'setChildren', false);
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Objective::addMeshDescriptor
     */
    public function testAddMeshDescriptor()
    {
        $this->entityCollectionAddTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Objective::removeMeshDescriptor
     */
    public function testRemoveMeshDescriptor()
    {
        $this->entityCollectionRemoveTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Objective::getMeshDescriptors
     */
    public function testGetMeshDescriptors()
    {
        $this->entityCollectionSetTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Objective::addParent
     */
    public function testAddParent()
    {
        $this->entityCollectionAddTest('parent', 'Objective');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Objective::removeParent
     */
    public function testRemoveParent()
    {
        $this->entityCollectionRemoveTest('parent', 'Objective');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Objective::getParents
     * @covers \Ilios\CoreBundle\Entity\Objective::setParents
     */
    public function testGetParents()
    {
        $this->entityCollectionSetTest('parent', 'Objective');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Objective::setAncestor
     * @covers \Ilios\CoreBundle\Entity\Objective::getAncestor
     */
    public function testSetAncestor()
    {
        $this->entitySetTest('ancestor', 'Objective');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Objective::getAncestorOrSelf
     */
    public function testGetAncestorOrSelfWithAncestor()
    {
        $ancestor = m::mock('Ilios\CoreBundle\Entity\Objective');
        $this->object->setAncestor($ancestor);
        $this->assertSame($ancestor, $this->object->getAncestorOrSelf());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Objective::getAncestorOrSelf
     */
    public function testGetAncestorOrSelfWithNoAncestor()
    {
        $this->assertSame($this->object, $this->object->getAncestorOrSelf());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Objective::addDescendant
     */
    public function testAddDescendant()
    {
        $this->entityCollectionAddTest('descendant', 'Objective');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Objective::removeDescendant
     */
    public function testRemoveDescendant()
    {
        $this->entityCollectionRemoveTest('descendant', 'Objective');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Objective::getDescendants
     * @covers \Ilios\CoreBundle\Entity\Objective::setDescendants
     */
    public function testGetDescendants()
    {
        $this->entityCollectionSetTest('descendant', 'Objective');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Objective::setPosition
     * @covers \Ilios\CoreBundle\Entity\Objective::getPosition
     */
    public function testSetPosition()
    {
        $this->basicSetTest('position', 'integer');
    }
}
