<?php
namespace Ilios\CoreBundle\Tests\Entity;


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
     * @covers Ilios\CoreBundle\Entity\SessionLearningMaterial::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getMeshDescriptors());
    }
    
    /**
     * @covers Ilios\CoreBundle\Entity\SessionLearningMaterial::getSessionLearningMaterialId
     */
    public function testGetSessionLearningMaterialId()
    {
        $this->basicGetTest('sessionLearningMaterialId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionLearningMaterial::setNotes
     */
    public function testSetNotes()
    {
        $this->basicSetTest('notes', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionLearningMaterial::getNotes
     */
    public function testGetNotes()
    {
        $this->basicGetTest('notes', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionLearningMaterial::setRequired
     */
    public function testSetRequired()
    {
        $this->basicSetTest('required', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionLearningMaterial::getRequired
     */
    public function testGetRequired()
    {
        $this->basicGetTest('required', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionLearningMaterial::setNotesArePublic
     */
    public function testSetNotesArePublic()
    {
        $this->basicSetTest('notesArePublic', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionLearningMaterial::getNotesArePublic
     */
    public function testGetNotesArePublic()
    {
        $this->basicGetTest('notesArePublic', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionLearningMaterial::setSession
     */
    public function testSetSession()
    {
        $this->entitySetTest('session', 'Session');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionLearningMaterial::getSession
     */
    public function testGetSession()
    {
        $this->entityGetTest('session', 'Session');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionLearningMaterial::setLearningMaterial
     */
    public function testSetLearningMaterial()
    {
        $this->entitySetTest('learningMaterial', "LearningMaterial");
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionLearningMaterial::getLearningMaterial
     */
    public function testGetLearningMaterial()
    {
        $this->entityGetTest('learningMaterial', "LearningMaterial");
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionLearningMaterial::addMeshDescriptor
     */
    public function testAddMeshDescriptor()
    {
        $this->entityCollectionAddTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionLearningMaterial::removeMeshDescriptor
     */
    public function testRemoveMeshDescriptor()
    {
        $this->entityCollectionRemoveTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionLearningMaterial::getMeshDescriptors
     */
    public function testGetMeshDescriptors()
    {
        $this->entityCollectionGetTest('meshDescriptor', 'MeshDescriptor');
    }
}
