<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\Objective;
use Mockery as m;

/**
 * Tests for Model Objective
 */
class ObjectiveTest extends BaseModel
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
     * @covers Ilios\CoreBundle\Model\Objective::getObjectiveId
     */
    public function testGetObjectiveId()
    {
        $this->basicGetTest('objectiveId', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Objective::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Objective::getTitle
     */
    public function testGetTitle()
    {
        $this->basicGetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Objective::setCompetency
     */
    public function testSetCompetency()
    {
        $this->modelSetTest('competency', 'Competency');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Objective::getCompetency
     */
    public function testGetCompetency()
    {
        $this->modelGetTest('competency', 'Competency');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Objective::getCompetencyId
     */
    public function testGetCompetencyId()
    {
        $obj = m::mock('Ilios\CoreBundle\Model\Competency');
        $obj->shouldReceive('getCompetencyId')->times(1)->andReturn(13);
        $this->object->setCompetency($obj);
        $this->assertSame(13, $this->object->getCompetencyId());
    }

    /**
     * @covers Ilios\CoreBundle\Model\Objective::addCourse
     */
    public function testAddCourse()
    {
        $this->modelCollectionAddTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Objective::removeCourse
     */
    public function testRemoveCourse()
    {
        $this->modelCollectionRemoveTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Objective::getCourses
     */
    public function testGetCourses()
    {
        $this->modelCollectionGetTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Objective::addProgramYear
     */
    public function testAddProgramYear()
    {
        $this->modelCollectionAddTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Objective::removeProgramYear
     */
    public function testRemoveProgramYear()
    {
        $this->modelCollectionRemoveTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Objective::getProgramYears
     */
    public function testGetProgramYears()
    {
        $this->modelCollectionGetTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Objective::addSession
     */
    public function testAddSession()
    {
        $this->modelCollectionAddTest('session', 'Session');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Objective::removeSession
     */
    public function testRemoveSession()
    {
        $this->modelCollectionRemoveTest('session', 'Session');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Objective::getSessions
     */
    public function testGetSessions()
    {
        $this->modelCollectionGetTest('session', 'Session');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Objective::addChild
     */
    public function testAddChild()
    {
        $this->modelCollectionAddTest('children', 'Objective', 'getChildren', 'addChild');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Objective::removeChild
     */
    public function testRemoveChild()
    {
        $this->modelCollectionRemoveTest('children', 'Objective', 'getChildren', 'addChild', 'removeChild');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Objective::getChildren
     */
    public function testGetChildren()
    {
        $this->modelCollectionGetTest('children', 'Objective', 'getChildren', false);
    }

    /**
     * @covers Ilios\CoreBundle\Model\Objective::addMeshDescriptor
     */
    public function testAddMeshDescriptor()
    {
        $this->modelCollectionAddTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Objective::removeMeshDescriptor
     */
    public function testRemoveMeshDescriptor()
    {
        $this->modelCollectionRemoveTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Objective::getMeshDescriptors
     */
    public function testGetMeshDescriptors()
    {
        $this->modelCollectionGetTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Objective::addParent
     */
    public function testAddParent()
    {
        $this->modelCollectionAddTest('parent', 'Objective');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Objective::removeParent
     */
    public function testRemoveParent()
    {
        $this->modelCollectionRemoveTest('parent', 'Objective');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Objective::getParents
     */
    public function testGetParents()
    {
        $this->modelCollectionGetTest('parent', 'Objective');
    }
}
