<?php
namespace Ilios\CoreBundle\Tests\Entity;

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

    /**
     * @covers Ilios\CoreBundle\Entity\Objective::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Objective::setCompetency
     */
    public function testSetCompetency()
    {
        $this->entitySetTest('competency', 'Competency');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Objective::addCourse
     */
    public function testAddCourse()
    {
        $this->entityCollectionAddTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Objective::removeCourse
     */
    public function testRemoveCourse()
    {
        $this->entityCollectionRemoveTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Objective::getCourses
     */
    public function testGetCourses()
    {
        $this->entityCollectionSetTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Objective::addProgramYear
     */
    public function testAddProgramYear()
    {
        $this->entityCollectionAddTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Objective::removeProgramYear
     */
    public function testRemoveProgramYear()
    {
        $this->entityCollectionRemoveTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Objective::getProgramYears
     */
    public function testGetProgramYears()
    {
        $this->entityCollectionSetTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Objective::addSession
     */
    public function testAddSession()
    {
        $this->entityCollectionAddTest('session', 'Session');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Objective::removeSession
     */
    public function testRemoveSession()
    {
        $this->entityCollectionRemoveTest('session', 'Session');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Objective::getSessions
     */
    public function testGetSessions()
    {
        $this->entityCollectionSetTest('session', 'Session');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Objective::addChild
     */
    public function testAddChild()
    {
        $this->entityCollectionAddTest('children', 'Objective', 'getChildren', 'addChild');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Objective::removeChild
     */
    public function testRemoveChild()
    {
        $this->entityCollectionRemoveTest('children', 'Objective', 'getChildren', 'addChild', 'removeChild');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Objective::getChildren
     */
    public function testGetChildren()
    {
        $this->entityCollectionSetTest('children', 'Objective', 'getChildren', 'setChildren', false);
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Objective::addMeshDescriptor
     */
    public function testAddMeshDescriptor()
    {
        $this->entityCollectionAddTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Objective::removeMeshDescriptor
     */
    public function testRemoveMeshDescriptor()
    {
        $this->entityCollectionRemoveTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Objective::getMeshDescriptors
     */
    public function testGetMeshDescriptors()
    {
        $this->entityCollectionSetTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Objective::addParent
     */
    public function testAddParent()
    {
        $this->entityCollectionAddTest('parent', 'Objective');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Objective::removeParent
     */
    public function testRemoveParent()
    {
        $this->entityCollectionRemoveTest('parent', 'Objective');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Objective::getParents
     */
    public function testGetParents()
    {
        $this->entityCollectionSetTest('parent', 'Objective');
    }
}
