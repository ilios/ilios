<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\SessionLearningMaterial;
use Mockery as m;

/**
 * Tests for Entity SessionLearningMaterial
 */
class SessionLearningMaterialTest extends EntityBase
{
    /**
     * @var SessionLearningMaterial
     */
    protected $object;

    /**
     * Instantiate a SessionLearningMaterial object
     */
    protected function setUp()
    {
        $this->object = new SessionLearningMaterial;
    }

    /**
     * @covers \AppBundle\Entity\SessionLearningMaterial::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getMeshDescriptors());
    }

    /**
     * @covers \AppBundle\Entity\SessionLearningMaterial::setNotes
     * @covers \AppBundle\Entity\SessionLearningMaterial::getNotes
     */
    public function testSetNotes()
    {
        $this->basicSetTest('notes', 'string');
    }

    /**
     * @covers \AppBundle\Entity\SessionLearningMaterial::setRequired
     * @covers \AppBundle\Entity\SessionLearningMaterial::isRequired
     */
    public function testSetRequired()
    {
        $this->booleanSetTest('required');
    }

    /**
     * @covers \AppBundle\Entity\SessionLearningMaterial::setPublicNotes
     * @covers \AppBundle\Entity\SessionLearningMaterial::hasPublicNotes
     */
    public function testSetNotesArePublic()
    {
        $this->booleanSetTest('publicNotes', false);
    }

    /**
     * @covers \AppBundle\Entity\SessionLearningMaterial::setSession
     * @covers \AppBundle\Entity\SessionLearningMaterial::getSession
     */
    public function testSetSession()
    {
        $this->entitySetTest('session', 'Session');
    }

    /**
     * @covers \AppBundle\Entity\SessionLearningMaterial::setLearningMaterial
     * @covers \AppBundle\Entity\SessionLearningMaterial::getLearningMaterial
     */
    public function testSetLearningMaterial()
    {
        $this->entitySetTest('learningMaterial', "LearningMaterial");
    }

    /**
     * @covers \AppBundle\Entity\SessionLearningMaterial::addMeshDescriptor
     */
    public function testAddMeshDescriptor()
    {
        $this->entityCollectionAddTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \AppBundle\Entity\SessionLearningMaterial::removeMeshDescriptor
     */
    public function testRemoveMeshDescriptor()
    {
        $this->entityCollectionRemoveTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \AppBundle\Entity\SessionLearningMaterial::getMeshDescriptors
     */
    public function testGetMeshDescriptors()
    {
        $this->entityCollectionSetTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \AppBundle\Entity\SessionLearningMaterial::setPosition
     * @covers \AppBundle\Entity\SessionLearningMaterial::getPosition
     */
    public function testSetPosition()
    {
        $this->basicSetTest('position', 'integer');
    }

    /**
     * @covers \AppBundle\Entity\SessionLearningMaterial::setStartDate
     * @covers \AppBundle\Entity\SessionLearningMaterial::getStartDate
     */
    public function testSetStartDate()
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    /**
     * @covers \AppBundle\Entity\SessionLearningMaterial::setEndDate
     * @covers \AppBundle\Entity\SessionLearningMaterial::getEndDate
     */
    public function testSetEndDate()
    {
        $this->basicSetTest('endDate', 'datetime');
    }
}
