<?php
namespace Ilios\CoreBundle\Tests\Entity;


use Ilios\CoreBundle\Entity\Session;
use Mockery as m;

/**
 * Tests for Entity Session
 */
class SessionTest extends EntityBase
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
     * @covers Ilios\CoreBundle\Entity\Session::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getDisciplines());
        $this->assertEmpty($this->object->getMeshDescriptors());
        $this->assertEmpty($this->object->getObjectives());
    }
    
    /**
     * @covers Ilios\CoreBundle\Entity\Session::getSessionId
     */
    public function testGetSessionId()
    {
        $this->basicGetTest('sessionId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::getTitle
     */
    public function testGetTitle()
    {
        $this->basicGetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::setAttireRequired
     */
    public function testSetAttireRequired()
    {
        $this->basicSetTest('attireRequired', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::getAttireRequired
     */
    public function testGetAttireRequired()
    {
        $this->basicGetTest('attireRequired', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::setEquipmentRequired
     */
    public function testSetEquipmentRequired()
    {
        $this->basicSetTest('equipmentRequired', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::getEquipmentRequired
     */
    public function testGetEquipmentRequired()
    {
        $this->basicGetTest('equipmentRequired', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::setSupplemental
     */
    public function testSetSupplemental()
    {
        $this->basicSetTest('supplemental', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::getSupplemental
     */
    public function testGetSupplemental()
    {
        $this->basicGetTest('supplemental', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::setDeleted
     */
    public function testSetDeleted()
    {
        $this->basicSetTest('deleted', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::getDeleted
     */
    public function testGetDeleted()
    {
        $this->basicGetTest('deleted', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::setPublishedAsTbd
     */
    public function testSetPublishedAsTbd()
    {
        $this->basicSetTest('publishedAsTbd', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::getPublishedAsTbd
     */
    public function testGetPublishedAsTbd()
    {
        $this->basicGetTest('publishedAsTbd', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::setLastUpdatedOn
     */
    public function testSetLastUpdatedOn()
    {
        $this->basicSetTest('lastUpdatedOn', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::getLastUpdatedOn
     */
    public function testGetLastUpdatedOn()
    {
        $this->basicGetTest('lastUpdatedOn', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::setSessionType
     */
    public function testSetSessionType()
    {
        $this->entitySetTest('sessionType', "SessionType");
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::getSessionType
     */
    public function testGetSessionType()
    {
        $this->entityGetTest('sessionType', "SessionType");
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::setCourse
     */
    public function testSetCourse()
    {
        $this->entitySetTest('course', "Course");
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::getCourse
     */
    public function testGetCourse()
    {
        $this->entityGetTest('course', "Course");
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::setIlmSessionFacet
     */
    public function testSetIlmSessionFacet()
    {
        $this->entitySetTest('ilmSessionFacet', "IlmSessionFacet");
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::getIlmSessionFacet
     */
    public function testGetIlmSessionFacet()
    {
        $this->entityGetTest('ilmSessionFacet', "IlmSessionFacet");
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::addDiscipline
     */
    public function testAddDiscipline()
    {
        $this->entityCollectionAddTest('discipline', 'Discipline');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::removeDiscipline
     */
    public function testRemoveDiscipline()
    {
        $this->entityCollectionRemoveTest('discipline', 'Discipline');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::getDisciplines
     */
    public function testGetDisciplines()
    {
        $this->entityCollectionGetTest('discipline', 'Discipline');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::addObjective
     */
    public function testAddObjective()
    {
        $this->entityCollectionAddTest('objective', 'Objective');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::removeObjective
     */
    public function testRemoveObjective()
    {
        $this->entityCollectionRemoveTest('objective', 'Objective');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::getObjectives
     */
    public function testGetObjectives()
    {
        $this->entityCollectionGetTest('objective', 'Objective');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::addMeshDescriptor
     */
    public function testAddMeshDescriptor()
    {
        $this->entityCollectionAddTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::removeMeshDescriptor
     */
    public function testRemoveMeshDescriptor()
    {
        $this->entityCollectionRemoveTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::getMeshDescriptors
     */
    public function testGetMeshDescriptors()
    {
        $this->entityCollectionGetTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::getPublishEvent
     */
    public function testGetPublishEvent()
    {
         $this->entityGetTest('publishEvent', 'PublishEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Session::setPublishEvent
     */
    public function testSetPublishEvent()
    {
        $this->entitySetTest('publishEvent', 'PublishEvent');
    }
}
