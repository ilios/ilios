<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\MeshDescriptor;
use Mockery as m;

/**
 * Tests for Model MeshDescriptor
 */
class MeshDescriptorTest extends BaseModel
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

    /**
     * @covers Ilios\CoreBundle\Model\MeshDescriptor::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getCourses());
        $this->assertEmpty($this->object->getCourseLearningMaterials());
        $this->assertEmpty($this->object->getObjectives());
        $this->assertEmpty($this->object->getSessions());
        $this->assertEmpty($this->object->getSessionLearningMaterials());
    }
    
    /**
     * @covers Ilios\CoreBundle\Model\MeshDescriptor::setMeshDescriptorUid
     */
    public function testSetMeshDescriptorUid()
    {
        $this->basicSetTest('meshDescriptorUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshDescriptor::getMeshDescriptorUid
     */
    public function testGetMeshDescriptorUid()
    {
        $this->basicGetTest('meshDescriptorUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshDescriptor::setName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshDescriptor::getName
     */
    public function testGetName()
    {
        $this->basicGetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshDescriptor::setAnnotation
     */
    public function testSetAnnotation()
    {
        $this->basicSetTest('annotation', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshDescriptor::getAnnotation
     */
    public function testGetAnnotation()
    {
        $this->basicGetTest('annotation', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshDescriptor::setCreatedAt
     */
    public function testSetCreatedAt()
    {
        $this->basicSetTest('createdAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshDescriptor::getCreatedAt
     */
    public function testGetCreatedAt()
    {
        $this->basicGetTest('createdAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshDescriptor::setUpdatedAt
     */
    public function testSetUpdatedAt()
    {
        $this->basicSetTest('updatedAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshDescriptor::getUpdatedAt
     */
    public function testGetUpdatedAt()
    {
        $this->basicGetTest('updatedAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshDescriptor::addCourse
     */
    public function testAddCourse()
    {
        $this->modelCollectionAddTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshDescriptor::removeCourse
     */
    public function testRemoveCourse()
    {
        $this->modelCollectionRemoveTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshDescriptor::getCourses
     */
    public function testGetCourses()
    {
        $this->modelCollectionGetTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshDescriptor::addObjective
     */
    public function testAddObjective()
    {
        $this->modelCollectionAddTest('objective', 'Objective');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshDescriptor::removeObjective
     */
    public function testRemoveObjective()
    {
        $this->modelCollectionRemoveTest('objective', 'Objective');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshDescriptor::getObjectives
     */
    public function testGetObjectives()
    {
        $this->modelCollectionGetTest('objective', 'Objective');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshDescriptor::addSession
     */
    public function testAddSession()
    {
        $this->modelCollectionAddTest('session', 'Session');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshDescriptor::removeSession
     */
    public function testRemoveSession()
    {
        $this->modelCollectionRemoveTest('session', 'Session');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshDescriptor::getSessions
     */
    public function testGetSessions()
    {
        $this->modelCollectionGetTest('session', 'Session');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshDescriptor::addSessionLearningMaterial
     */
    public function testAddSessionLearningMaterial()
    {
        $this->modelCollectionAddTest('sessionLearningMaterial', 'SessionLearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshDescriptor::removeSessionLearningMaterial
     */
    public function testRemoveSessionLearningMaterial()
    {
        $this->modelCollectionRemoveTest('sessionLearningMaterial', 'SessionLearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshDescriptor::getSessionLearningMaterials
     */
    public function testGetSessionLearningMaterials()
    {
        $this->modelCollectionGetTest('sessionLearningMaterial', 'SessionLearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshDescriptor::addCourseLearningMaterial
     */
    public function testAddCourseLearningMaterial()
    {
        $this->modelCollectionAddTest('courseLearningMaterial', 'CourseLearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshDescriptor::removeCourseLearningMaterial
     */
    public function testRemoveCourseLearningMaterial()
    {
        $this->modelCollectionRemoveTest('courseLearningMaterial', 'CourseLearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Model\MeshDescriptor::getCourseLearningMaterials
     */
    public function testGetCourseLearningMaterials()
    {
        $this->modelCollectionGetTest('courseLearningMaterial', 'CourseLearningMaterial');
    }
}
