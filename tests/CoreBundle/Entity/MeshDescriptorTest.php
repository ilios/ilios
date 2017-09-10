<?php
namespace Tests\CoreBundle\Entity;

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
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getCourses());
        $this->assertEmpty($this->object->getCourseLearningMaterials());
        $this->assertEmpty($this->object->getObjectives());
        $this->assertEmpty($this->object->getSessions());
        $this->assertEmpty($this->object->getSessionLearningMaterials());
        $this->assertEmpty($this->object->getTrees());
        $now = new \DateTime();
        $createdAt = $this->object->getCreatedAt();
        $this->assertTrue($createdAt instanceof \DateTime);
        $diff = $now->diff($createdAt);
        $this->assertTrue($diff->s < 2);
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::setName
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::setAnnotation
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::getAnnotation
     */
    public function testSetAnnotation()
    {
        $this->basicSetTest('annotation', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::addCourse
     */
    public function testAddCourse()
    {
        $this->entityCollectionAddTest('course', 'Course', false, false, 'addMeshDescriptor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::removeCourse
     */
    public function testRemoveCourse()
    {
        $this->entityCollectionRemoveTest('course', 'Course', false, false, false, 'removeMeshDescriptor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::getCourses
     */
    public function testGetCourses()
    {
        $this->entityCollectionSetTest('course', 'Course', false, false, 'addMeshDescriptor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::addObjective
     */
    public function testAddObjective()
    {
        $this->entityCollectionAddTest('objective', 'Objective', false, false, 'addMeshDescriptor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::removeObjective
     */
    public function testRemoveObjective()
    {
        $this->entityCollectionRemoveTest('objective', 'Objective', false, false, false, 'removeMeshDescriptor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::setObjectives
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::getObjectives
     */
    public function testGetObjectives()
    {
        $this->entityCollectionSetTest('objective', 'Objective', false, false, 'addMeshDescriptor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::addSession
     */
    public function testAddSession()
    {
        $this->entityCollectionAddTest('session', 'Session', false, false, 'addMeshDescriptor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::removeSession
     */
    public function testRemoveSession()
    {
        $this->entityCollectionRemoveTest('session', 'Session', false, false, false, 'removeMeshDescriptor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::getSessions
     */
    public function testGetSessions()
    {
        $this->entityCollectionSetTest('session', 'Session', false, false, 'addMeshDescriptor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::addConcept
     */
    public function testAddConcept()
    {
        $this->entityCollectionAddTest('concept', 'MeshConcept', false, false, 'addDescriptor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::removeConcept
     */
    public function testRemoveConcept()
    {
        $this->entityCollectionRemoveTest('concept', 'MeshConcept', false, false, false, 'removeDescriptor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::getConcepts
     */
    public function testGetConcepts()
    {
        $this->entityCollectionSetTest('concept', 'MeshConcept', false, false, 'addDescriptor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::addQualifier
     */
    public function testAddQualifier()
    {
        $this->entityCollectionAddTest('qualifier', 'MeshQualifier', false, false, 'addDescriptor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::removeQualifier
     */
    public function testRemoveQualifier()
    {
        $this->entityCollectionRemoveTest('qualifier', 'MeshQualifier', false, false, false, 'removeDescriptor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::getQualifiers
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::setQualifiers
     */
    public function testGetQualifiers()
    {
        $this->entityCollectionSetTest('qualifier', 'MeshQualifier', false, false, 'addDescriptor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::addTree
     */
    public function testAddTree()
    {
        $this->entityCollectionAddTest('tree', 'MeshTree');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::removeTree
     */
    public function testRemoveTree()
    {
        $this->entityCollectionRemoveTest('tree', 'MeshTree');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::getTrees
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::setTrees
     */
    public function testGetTrees()
    {
        $this->entityCollectionSetTest('tree', 'MeshTree');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::addSessionLearningMaterial
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
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::removeSessionLearningMaterial
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
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::getSessionLearningMaterials
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::setSessionLearningMaterials
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
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::addCourseLearningMaterial
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
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::removeCourseLearningMaterial
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
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::getCourseLearningMaterials
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::setCourseLearningMaterials
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
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::stampUpdate
     */
    public function testStampUpdate()
    {
        $now = new \DateTime();
        $this->object->stampUpdate();
        $updatedAt = $this->object->getUpdatedAt();
        $this->assertTrue($updatedAt instanceof \DateTime);
        $diff = $now->diff($updatedAt);
        $this->assertTrue($diff->s < 2);
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::setDeleted
     * @covers \Ilios\CoreBundle\Entity\MeshDescriptor::isDeleted()
     */
    public function testSetPermuted()
    {
        $this->booleanSetTest('deleted');
    }
}
