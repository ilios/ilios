<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\MeshDescriptor;
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
     * @covers \AppBundle\Entity\MeshDescriptor::__construct
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
     * @covers \AppBundle\Entity\MeshDescriptor::setName
     * @covers \AppBundle\Entity\MeshDescriptor::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \AppBundle\Entity\MeshDescriptor::setAnnotation
     * @covers \AppBundle\Entity\MeshDescriptor::getAnnotation
     */
    public function testSetAnnotation()
    {
        $this->basicSetTest('annotation', 'string');
    }

    /**
     * @covers \AppBundle\Entity\MeshDescriptor::addCourse
     */
    public function testAddCourse()
    {
        $this->entityCollectionAddTest('course', 'Course', false, false, 'addMeshDescriptor');
    }

    /**
     * @covers \AppBundle\Entity\MeshDescriptor::removeCourse
     */
    public function testRemoveCourse()
    {
        $this->entityCollectionRemoveTest('course', 'Course', false, false, false, 'removeMeshDescriptor');
    }

    /**
     * @covers \AppBundle\Entity\MeshDescriptor::getCourses
     */
    public function testGetCourses()
    {
        $this->entityCollectionSetTest('course', 'Course', false, false, 'addMeshDescriptor');
    }

    /**
     * @covers \AppBundle\Entity\MeshDescriptor::addObjective
     */
    public function testAddObjective()
    {
        $this->entityCollectionAddTest('objective', 'Objective', false, false, 'addMeshDescriptor');
    }

    /**
     * @covers \AppBundle\Entity\MeshDescriptor::removeObjective
     */
    public function testRemoveObjective()
    {
        $this->entityCollectionRemoveTest('objective', 'Objective', false, false, false, 'removeMeshDescriptor');
    }

    /**
     * @covers \AppBundle\Entity\MeshDescriptor::setObjectives
     * @covers \AppBundle\Entity\MeshDescriptor::getObjectives
     */
    public function testGetObjectives()
    {
        $this->entityCollectionSetTest('objective', 'Objective', false, false, 'addMeshDescriptor');
    }

    /**
     * @covers \AppBundle\Entity\MeshDescriptor::addSession
     */
    public function testAddSession()
    {
        $this->entityCollectionAddTest('session', 'Session', false, false, 'addMeshDescriptor');
    }

    /**
     * @covers \AppBundle\Entity\MeshDescriptor::removeSession
     */
    public function testRemoveSession()
    {
        $this->entityCollectionRemoveTest('session', 'Session', false, false, false, 'removeMeshDescriptor');
    }

    /**
     * @covers \AppBundle\Entity\MeshDescriptor::getSessions
     */
    public function testGetSessions()
    {
        $this->entityCollectionSetTest('session', 'Session', false, false, 'addMeshDescriptor');
    }

    /**
     * @covers \AppBundle\Entity\MeshDescriptor::addConcept
     */
    public function testAddConcept()
    {
        $this->entityCollectionAddTest('concept', 'MeshConcept', false, false, 'addDescriptor');
    }

    /**
     * @covers \AppBundle\Entity\MeshDescriptor::removeConcept
     */
    public function testRemoveConcept()
    {
        $this->entityCollectionRemoveTest('concept', 'MeshConcept', false, false, false, 'removeDescriptor');
    }

    /**
     * @covers \AppBundle\Entity\MeshDescriptor::getConcepts
     */
    public function testGetConcepts()
    {
        $this->entityCollectionSetTest('concept', 'MeshConcept', false, false, 'addDescriptor');
    }

    /**
     * @covers \AppBundle\Entity\MeshDescriptor::addQualifier
     */
    public function testAddQualifier()
    {
        $this->entityCollectionAddTest('qualifier', 'MeshQualifier', false, false, 'addDescriptor');
    }

    /**
     * @covers \AppBundle\Entity\MeshDescriptor::removeQualifier
     */
    public function testRemoveQualifier()
    {
        $this->entityCollectionRemoveTest('qualifier', 'MeshQualifier', false, false, false, 'removeDescriptor');
    }

    /**
     * @covers \AppBundle\Entity\MeshDescriptor::getQualifiers
     * @covers \AppBundle\Entity\MeshDescriptor::setQualifiers
     */
    public function testGetQualifiers()
    {
        $this->entityCollectionSetTest('qualifier', 'MeshQualifier', false, false, 'addDescriptor');
    }

    /**
     * @covers \AppBundle\Entity\MeshDescriptor::addTree
     */
    public function testAddTree()
    {
        $this->entityCollectionAddTest('tree', 'MeshTree');
    }

    /**
     * @covers \AppBundle\Entity\MeshDescriptor::removeTree
     */
    public function testRemoveTree()
    {
        $this->entityCollectionRemoveTest('tree', 'MeshTree');
    }

    /**
     * @covers \AppBundle\Entity\MeshDescriptor::getTrees
     * @covers \AppBundle\Entity\MeshDescriptor::setTrees
     */
    public function testGetTrees()
    {
        $this->entityCollectionSetTest('tree', 'MeshTree');
    }

    /**
     * @covers \AppBundle\Entity\MeshDescriptor::addSessionLearningMaterial
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
     * @covers \AppBundle\Entity\MeshDescriptor::removeSessionLearningMaterial
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
     * @covers \AppBundle\Entity\MeshDescriptor::getSessionLearningMaterials
     * @covers \AppBundle\Entity\MeshDescriptor::setSessionLearningMaterials
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
     * @covers \AppBundle\Entity\MeshDescriptor::addCourseLearningMaterial
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
     * @covers \AppBundle\Entity\MeshDescriptor::removeCourseLearningMaterial
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
     * @covers \AppBundle\Entity\MeshDescriptor::getCourseLearningMaterials
     * @covers \AppBundle\Entity\MeshDescriptor::setCourseLearningMaterials
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
     * @covers \AppBundle\Entity\MeshDescriptor::stampUpdate
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
     * @covers \AppBundle\Entity\MeshDescriptor::setDeleted
     * @covers \AppBundle\Entity\MeshDescriptor::isDeleted()
     */
    public function testSetPermuted()
    {
        $this->booleanSetTest('deleted');
    }
}
