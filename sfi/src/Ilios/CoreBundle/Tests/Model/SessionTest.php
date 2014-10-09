<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\Session;
use Mockery as m;

/**
 * Tests for Model Session
 */
class SessionTest extends ModelBase
{
    /**
     * @var Session
     */
    protected $object;

    /**
     * Instantiate a Session object
     */
    protected function setUp()
    {
        $this->object = new Session;
    }

    /**
     * @covers Ilios\CoreBundle\Model\Session::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getDisciplines());
        $this->assertEmpty($this->object->getMeshDescriptors());
        $this->assertEmpty($this->object->getObjectives());
    }
    
    /**
     * @covers Ilios\CoreBundle\Model\Session::getSessionId
     */
    public function testGetSessionId()
    {
        $this->basicGetTest('sessionId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Session::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Session::getTitle
     */
    public function testGetTitle()
    {
        $this->basicGetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Session::setAttireRequired
     */
    public function testSetAttireRequired()
    {
        $this->basicSetTest('attireRequired', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Session::getAttireRequired
     */
    public function testGetAttireRequired()
    {
        $this->basicGetTest('attireRequired', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Session::setEquipmentRequired
     */
    public function testSetEquipmentRequired()
    {
        $this->basicSetTest('equipmentRequired', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Session::getEquipmentRequired
     */
    public function testGetEquipmentRequired()
    {
        $this->basicGetTest('equipmentRequired', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Session::setSupplemental
     */
    public function testSetSupplemental()
    {
        $this->basicSetTest('supplemental', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Session::getSupplemental
     */
    public function testGetSupplemental()
    {
        $this->basicGetTest('supplemental', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Session::setDeleted
     */
    public function testSetDeleted()
    {
        $this->basicSetTest('deleted', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Session::getDeleted
     */
    public function testGetDeleted()
    {
        $this->basicGetTest('deleted', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Session::setPublishedAsTbd
     */
    public function testSetPublishedAsTbd()
    {
        $this->basicSetTest('publishedAsTbd', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Session::getPublishedAsTbd
     */
    public function testGetPublishedAsTbd()
    {
        $this->basicGetTest('publishedAsTbd', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Session::setLastUpdatedOn
     */
    public function testSetLastUpdatedOn()
    {
        $this->basicSetTest('lastUpdatedOn', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Session::getLastUpdatedOn
     */
    public function testGetLastUpdatedOn()
    {
        $this->basicGetTest('lastUpdatedOn', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Session::setSessionType
     */
    public function testSetSessionType()
    {
        $this->modelSetTest('sessionType', "SessionType");
    }

    /**
     * @covers Ilios\CoreBundle\Model\Session::getSessionType
     */
    public function testGetSessionType()
    {
        $this->modelGetTest('sessionType', "SessionType");
    }

    /**
     * @covers Ilios\CoreBundle\Model\Session::setCourse
     */
    public function testSetCourse()
    {
        $this->modelSetTest('course', "Course");
    }

    /**
     * @covers Ilios\CoreBundle\Model\Session::getCourse
     */
    public function testGetCourse()
    {
        $this->modelGetTest('course', "Course");
    }

    /**
     * @covers Ilios\CoreBundle\Model\Session::setIlmSessionFacet
     */
    public function testSetIlmSessionFacet()
    {
        $this->modelSetTest('ilmSessionFacet', "IlmSessionFacet");
    }

    /**
     * @covers Ilios\CoreBundle\Model\Session::getIlmSessionFacet
     */
    public function testGetIlmSessionFacet()
    {
        $this->modelGetTest('ilmSessionFacet', "IlmSessionFacet");
    }

    /**
     * @covers Ilios\CoreBundle\Model\Session::addDiscipline
     */
    public function testAddDiscipline()
    {
        $this->modelCollectionAddTest('discipline', 'Discipline');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Session::removeDiscipline
     */
    public function testRemoveDiscipline()
    {
        $this->modelCollectionRemoveTest('discipline', 'Discipline');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Session::getDisciplines
     */
    public function testGetDisciplines()
    {
        $this->modelCollectionGetTest('discipline', 'Discipline');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Session::addObjective
     */
    public function testAddObjective()
    {
        $this->modelCollectionAddTest('objective', 'Objective');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Session::removeObjective
     */
    public function testRemoveObjective()
    {
        $this->modelCollectionRemoveTest('objective', 'Objective');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Session::getObjectives
     */
    public function testGetObjectives()
    {
        $this->modelCollectionGetTest('objective', 'Objective');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Session::addMeshDescriptor
     */
    public function testAddMeshDescriptor()
    {
        $this->modelCollectionAddTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Session::removeMeshDescriptor
     */
    public function testRemoveMeshDescriptor()
    {
        $this->modelCollectionRemoveTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Session::getMeshDescriptors
     */
    public function testGetMeshDescriptors()
    {
        $this->modelCollectionGetTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Session::getPublishEvent
     */
    public function testGetPublishEvent()
    {
         $this->modelGetTest('publishEvent', 'PublishEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Session::setPublishEvent
     */
    public function testSetPublishEvent()
    {
        $this->modelSetTest('publishEvent', 'PublishEvent');
    }
}
