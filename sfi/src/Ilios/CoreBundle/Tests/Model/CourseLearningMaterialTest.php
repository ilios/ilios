<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\CourseLearningMaterial;
use Mockery as m;

/**
 * Tests for Model CourseLearningMaterial
 */
class CourseLearningMaterialTest extends BaseModel
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
     * @covers Ilios\CoreBundle\Model\CourseLearningMaterial::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getMeshDescriptors());
    }

    /**
     * @covers Ilios\CoreBundle\Model\CourseLearningMaterial::getCourseLearningMaterialId
     */
    public function testGetCourseLearningMaterialId()
    {
        $this->basicGetTest('courseLearningMaterialId', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CourseLearningMaterial::setNotes
     */
    public function testSetNotes()
    {
        $this->basicSetTest('notes', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CourseLearningMaterial::getNotes
     */
    public function testGetNotes()
    {
        $this->basicGetTest('notes', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CourseLearningMaterial::setRequired
     */
    public function testSetRequired()
    {
        $this->basicSetTest('required', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CourseLearningMaterial::getRequired
     */
    public function testGetRequired()
    {
        $this->basicGetTest('required', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CourseLearningMaterial::setNotesArePublic
     */
    public function testSetNotesArePublic()
    {
        $this->basicSetTest('notesArePublic', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CourseLearningMaterial::getNotesArePublic
     */
    public function testGetNotesArePublic()
    {
        $this->basicGetTest('notesArePublic', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CourseLearningMaterial::setCourse
     */
    public function testSetCourse()
    {
        $this->modelSetTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CourseLearningMaterial::getCourse
     */
    public function testGetCourse()
    {
        $this->modelGetTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CourseLearningMaterial::setLearningMaterial
     */
    public function testSetLearningMaterial()
    {
        $this->modelSetTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CourseLearningMaterial::getLearningMaterial
     */
    public function testGetLearningMaterial()
    {
        $this->modelGetTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CourseLearningMaterial::addMeshDescriptor
     */
    public function testAddMeshDescriptor()
    {
        $this->modelCollectionAddTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CourseLearningMaterial::removeMeshDescriptor
     */
    public function testRemoveMeshDescriptor()
    {
        $this->modelCollectionRemoveTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CourseLearningMaterial::getMeshDescriptors
     */
    public function testGetMeshDescriptors()
    {
        $this->modelCollectionGetTest('meshDescriptor', 'MeshDescriptor');
    }
}
