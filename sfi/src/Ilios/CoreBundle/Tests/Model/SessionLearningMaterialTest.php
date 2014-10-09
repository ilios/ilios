<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\SessionLearningMaterial;
use Mockery as m;

/**
 * Tests for Model SessionLearningMaterial
 */
class SessionLearningMaterialTest extends ModelBase
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
     * @covers Ilios\CoreBundle\Model\SessionLearningMaterial::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getMeshDescriptors());
    }
    
    /**
     * @covers Ilios\CoreBundle\Model\SessionLearningMaterial::getSessionLearningMaterialId
     */
    public function testGetSessionLearningMaterialId()
    {
        $this->basicGetTest('sessionLearningMaterialId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\SessionLearningMaterial::setNotes
     */
    public function testSetNotes()
    {
        $this->basicSetTest('notes', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\SessionLearningMaterial::getNotes
     */
    public function testGetNotes()
    {
        $this->basicGetTest('notes', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\SessionLearningMaterial::setRequired
     */
    public function testSetRequired()
    {
        $this->basicSetTest('required', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\SessionLearningMaterial::getRequired
     */
    public function testGetRequired()
    {
        $this->basicGetTest('required', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\SessionLearningMaterial::setNotesArePublic
     */
    public function testSetNotesArePublic()
    {
        $this->basicSetTest('notesArePublic', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\SessionLearningMaterial::getNotesArePublic
     */
    public function testGetNotesArePublic()
    {
        $this->basicGetTest('notesArePublic', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\SessionLearningMaterial::setSession
     */
    public function testSetSession()
    {
        $this->modelSetTest('session', 'Session');
    }

    /**
     * @covers Ilios\CoreBundle\Model\SessionLearningMaterial::getSession
     */
    public function testGetSession()
    {
        $this->modelGetTest('session', 'Session');
    }

    /**
     * @covers Ilios\CoreBundle\Model\SessionLearningMaterial::setLearningMaterial
     */
    public function testSetLearningMaterial()
    {
        $this->modelSetTest('learningMaterial', "LearningMaterial");
    }

    /**
     * @covers Ilios\CoreBundle\Model\SessionLearningMaterial::getLearningMaterial
     */
    public function testGetLearningMaterial()
    {
        $this->modelGetTest('learningMaterial', "LearningMaterial");
    }

    /**
     * @covers Ilios\CoreBundle\Model\SessionLearningMaterial::addMeshDescriptor
     */
    public function testAddMeshDescriptor()
    {
        $this->modelCollectionAddTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers Ilios\CoreBundle\Model\SessionLearningMaterial::removeMeshDescriptor
     */
    public function testRemoveMeshDescriptor()
    {
        $this->modelCollectionRemoveTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers Ilios\CoreBundle\Model\SessionLearningMaterial::getMeshDescriptors
     */
    public function testGetMeshDescriptors()
    {
        $this->modelCollectionGetTest('meshDescriptor', 'MeshDescriptor');
    }
}
