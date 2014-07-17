<?php
namespace Ilios\CoreBundle\Tests\Entity;


use Ilios\CoreBundle\Entity\MeshDescriptor;
use Mockery as m;

/**
 * Tests for Entity MeshDescriptor
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

    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::__construct
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
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::setMeshDescriptorUid
     */
    public function testSetMeshDescriptorUid()
    {
        $this->basicSetTest('meshDescriptorUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::getMeshDescriptorUid
     */
    public function testGetMeshDescriptorUid()
    {
        $this->basicGetTest('meshDescriptorUid', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::setName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::getName
     */
    public function testGetName()
    {
        $this->basicGetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::setAnnotation
     */
    public function testSetAnnotation()
    {
        $this->basicSetTest('annotation', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::getAnnotation
     */
    public function testGetAnnotation()
    {
        $this->basicGetTest('annotation', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::setCreatedAt
     */
    public function testSetCreatedAt()
    {
        $this->basicSetTest('createdAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::getCreatedAt
     */
    public function testGetCreatedAt()
    {
        $this->basicGetTest('createdAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::setUpdatedAt
     */
    public function testSetUpdatedAt()
    {
        $this->basicSetTest('updatedAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::getUpdatedAt
     */
    public function testGetUpdatedAt()
    {
        $this->basicGetTest('updatedAt', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::addCourse
     */
    public function testAddCourse()
    {
        $this->entityCollectionAddTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::removeCourse
     */
    public function testRemoveCourse()
    {
        $this->entityCollectionRemoveTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::getCourses
     */
    public function testGetCourses()
    {
        $this->entityCollectionGetTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::addObjective
     */
    public function testAddObjective()
    {
        $this->entityCollectionAddTest('objective', 'Objective');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::removeObjective
     */
    public function testRemoveObjective()
    {
        $this->entityCollectionRemoveTest('objective', 'Objective');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::getObjectives
     */
    public function testGetObjectives()
    {
        $this->entityCollectionGetTest('objective', 'Objective');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::addSession
     */
    public function testAddSession()
    {
        $this->entityCollectionAddTest('session', 'Session');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::removeSession
     */
    public function testRemoveSession()
    {
        $this->entityCollectionRemoveTest('session', 'Session');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::getSessions
     */
    public function testGetSessions()
    {
        $this->entityCollectionGetTest('session', 'Session');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::addSessionLearningMaterial
     */
    public function testAddSessionLearningMaterial()
    {
        $this->entityCollectionAddTest('sessionLearningMaterial', 'SessionLearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::removeSessionLearningMaterial
     */
    public function testRemoveSessionLearningMaterial()
    {
        $this->entityCollectionRemoveTest('sessionLearningMaterial', 'SessionLearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::getSessionLearningMaterials
     */
    public function testGetSessionLearningMaterials()
    {
        $this->entityCollectionGetTest('sessionLearningMaterial', 'SessionLearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::addCourseLearningMaterial
     */
    public function testAddCourseLearningMaterial()
    {
        $this->entityCollectionAddTest('courseLearningMaterial', 'CourseLearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::removeCourseLearningMaterial
     */
    public function testRemoveCourseLearningMaterial()
    {
        $this->entityCollectionRemoveTest('courseLearningMaterial', 'CourseLearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::getCourseLearningMaterials
     */
    public function testGetCourseLearningMaterials()
    {
        $this->entityCollectionGetTest('courseLearningMaterial', 'CourseLearningMaterial');
    }
}
