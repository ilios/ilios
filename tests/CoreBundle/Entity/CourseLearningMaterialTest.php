<?php
namespace Tests\CoreBundle\Entity;

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
        $this->assertFalse($this->object->hasPublicNotes());
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CourseLearningMaterial::setNotes
     * @covers Ilios\CoreBundle\Entity\CourseLearningMaterial::getNotes
     */
    public function testSetNotes()
    {
        $this->basicSetTest('notes', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CourseLearningMaterial::setRequired
     * @covers Ilios\CoreBundle\Entity\CourseLearningMaterial::isRequired
     */
    public function testSetRequired()
    {
        $this->booleanSetTest('required');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CourseLearningMaterial::setPublicNotes
     * @covers Ilios\CoreBundle\Entity\CourseLearningMaterial::hasPublicNotes
     */
    public function testSetPublicNotes()
    {
        $this->booleanSetTest('publicNotes', false);
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CourseLearningMaterial::setCourse
     * @covers Ilios\CoreBundle\Entity\CourseLearningMaterial::getCourse
     */
    public function testSetCourse()
    {
        $this->entitySetTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CourseLearningMaterial::setLearningMaterial
     * @covers Ilios\CoreBundle\Entity\CourseLearningMaterial::getLearningMaterial
     */
    public function testSetLearningMaterial()
    {
        $this->entitySetTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CourseLearningMaterial::addMeshDescriptor
     */
    public function testAddMeshDescriptor()
    {
        $this->entityCollectionAddTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CourseLearningMaterial::getMeshDescriptors
     */
    public function testGetMeshDescriptors()
    {
        $this->entityCollectionSetTest('meshDescriptor', 'MeshDescriptor');
    }
}
