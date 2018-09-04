<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\CourseLearningMaterial;
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
     * @covers \AppBundle\Entity\CourseLearningMaterial::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getMeshDescriptors());
        $this->assertFalse($this->object->hasPublicNotes());
    }

    /**
     * @covers \AppBundle\Entity\CourseLearningMaterial::setNotes
     * @covers \AppBundle\Entity\CourseLearningMaterial::getNotes
     */
    public function testSetNotes()
    {
        $this->basicSetTest('notes', 'string');
    }

    /**
     * @covers \AppBundle\Entity\CourseLearningMaterial::setRequired
     * @covers \AppBundle\Entity\CourseLearningMaterial::isRequired
     */
    public function testSetRequired()
    {
        $this->booleanSetTest('required');
    }

    /**
     * @covers \AppBundle\Entity\CourseLearningMaterial::setPublicNotes
     * @covers \AppBundle\Entity\CourseLearningMaterial::hasPublicNotes
     */
    public function testSetPublicNotes()
    {
        $this->booleanSetTest('publicNotes', false);
    }

    /**
     * @covers \AppBundle\Entity\CourseLearningMaterial::setCourse
     * @covers \AppBundle\Entity\CourseLearningMaterial::getCourse
     */
    public function testSetCourse()
    {
        $this->entitySetTest('course', 'Course');
    }

    /**
     * @covers \AppBundle\Entity\CourseLearningMaterial::setLearningMaterial
     * @covers \AppBundle\Entity\CourseLearningMaterial::getLearningMaterial
     */
    public function testSetLearningMaterial()
    {
        $this->entitySetTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers \AppBundle\Entity\CourseLearningMaterial::addMeshDescriptor
     */
    public function testAddMeshDescriptor()
    {
        $this->entityCollectionAddTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \AppBundle\Entity\CourseLearningMaterial::removeMeshDescriptor
     */
    public function testRemoveMeshDescriptor()
    {
        $this->entityCollectionRemoveTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \AppBundle\Entity\CourseLearningMaterial::getMeshDescriptors
     */
    public function testGetMeshDescriptors()
    {
        $this->entityCollectionSetTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \AppBundle\Entity\CourseLearningMaterial::setPosition
     * @covers \AppBundle\Entity\CourseLearningMaterial::getPosition
     */
    public function testSetPosition()
    {
        $this->basicSetTest('position', 'integer');
    }

    /**
     * @covers \AppBundle\Entity\CourseLearningMaterial::setStartDate
     * @covers \AppBundle\Entity\CourseLearningMaterial::getStartDate
     */
    public function testSetStartDate()
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    /**
     * @covers \AppBundle\Entity\CourseLearningMaterial::setEndDate
     * @covers \AppBundle\Entity\CourseLearningMaterial::getEndDate
     */
    public function testSetEndDate()
    {
        $this->basicSetTest('endDate', 'datetime');
    }
}
