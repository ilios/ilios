<?php
namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\SessionLearningMaterial;
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
     * @covers \Ilios\CoreBundle\Entity\SessionLearningMaterial::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getMeshDescriptors());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\SessionLearningMaterial::setNotes
     * @covers \Ilios\CoreBundle\Entity\SessionLearningMaterial::getNotes
     */
    public function testSetNotes()
    {
        $this->basicSetTest('notes', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\SessionLearningMaterial::setRequired
     * @covers \Ilios\CoreBundle\Entity\SessionLearningMaterial::isRequired
     */
    public function testSetRequired()
    {
        $this->booleanSetTest('required');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\SessionLearningMaterial::setPublicNotes
     * @covers \Ilios\CoreBundle\Entity\SessionLearningMaterial::hasPublicNotes
     */
    public function testSetNotesArePublic()
    {
        $this->booleanSetTest('publicNotes', false);
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\SessionLearningMaterial::setSession
     * @covers \Ilios\CoreBundle\Entity\SessionLearningMaterial::getSession
     */
    public function testSetSession()
    {
        $this->entitySetTest('session', 'Session');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\SessionLearningMaterial::setLearningMaterial
     * @covers \Ilios\CoreBundle\Entity\SessionLearningMaterial::getLearningMaterial
     */
    public function testSetLearningMaterial()
    {
        $this->entitySetTest('learningMaterial', "LearningMaterial");
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\SessionLearningMaterial::addMeshDescriptor
     */
    public function testAddMeshDescriptor()
    {
        $this->entityCollectionAddTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\SessionLearningMaterial::removeMeshDescriptor
     */
    public function testRemoveMeshDescriptor()
    {
        $this->entityCollectionRemoveTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\SessionLearningMaterial::getMeshDescriptors
     */
    public function testGetMeshDescriptors()
    {
        $this->entityCollectionSetTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\SessionLearningMaterial::setPosition
     * @covers \Ilios\CoreBundle\Entity\SessionLearningMaterial::getPosition
     */
    public function testSetPosition()
    {
        $this->basicSetTest('position', 'integer');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\SessionLearningMaterial::setStartDate
     * @covers \Ilios\CoreBundle\Entity\SessionLearningMaterial::getStartDate
     */
    public function testSetStartDate()
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\SessionLearningMaterial::setEndDate
     * @covers \Ilios\CoreBundle\Entity\SessionLearningMaterial::getEndDate
     */
    public function testSetEndDate()
    {
        $this->basicSetTest('endDate', 'datetime');
    }
}
