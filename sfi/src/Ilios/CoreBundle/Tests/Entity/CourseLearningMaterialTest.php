<?php
namespace Ilios\CoreBundle\Tests\Entity;


use Ilios\CoreBundle\Entity\CourseLearningMaterial;
use Mockery as m;

/**
 * Tests for Entity CourseLearningMaterial
 */
class CourseLearningMaterialTest extends EntityBase
{
    /**
     * @var CourseLearningMaterial
     */
    protected $object;

    /**
     * Instantiate a CourseLearningMaterial object
     */
    protected function setUp()
    {
        $this->object = new CourseLearningMaterial;
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CourseLearningMaterial::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getMeshDescriptors());
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CourseLearningMaterial::getCourseLearningMaterialId
     */
    public function testGetCourseLearningMaterialId()
    {
        $this->basicGetTest('courseLearningMaterialId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CourseLearningMaterial::setNotes
     */
    public function testSetNotes()
    {
        $this->basicSetTest('notes', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CourseLearningMaterial::getNotes
     */
    public function testGetNotes()
    {
        $this->basicGetTest('notes', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CourseLearningMaterial::setRequired
     */
    public function testSetRequired()
    {
        $this->basicSetTest('required', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CourseLearningMaterial::getRequired
     */
    public function testGetRequired()
    {
        $this->basicGetTest('required', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CourseLearningMaterial::setNotesArePublic
     */
    public function testSetNotesArePublic()
    {
        $this->basicSetTest('notesArePublic', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CourseLearningMaterial::getNotesArePublic
     */
    public function testGetNotesArePublic()
    {
        $this->basicGetTest('notesArePublic', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CourseLearningMaterial::setCourse
     */
    public function testSetCourse()
    {
        $this->entitySetTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CourseLearningMaterial::getCourse
     */
    public function testGetCourse()
    {
        $this->entityGetTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CourseLearningMaterial::setLearningMaterial
     */
    public function testSetLearningMaterial()
    {
        $this->entitySetTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CourseLearningMaterial::getLearningMaterial
     */
    public function testGetLearningMaterial()
    {
        $this->entityGetTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CourseLearningMaterial::addMeshDescriptor
     */
    public function testAddMeshDescriptor()
    {
        $this->entityCollectionAddTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CourseLearningMaterial::removeMeshDescriptor
     */
    public function testRemoveMeshDescriptor()
    {
        $this->entityCollectionRemoveTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CourseLearningMaterial::getMeshDescriptors
     */
    public function testGetMeshDescriptors()
    {
        $this->entityCollectionGetTest('meshDescriptor', 'MeshDescriptor');
    }
}
