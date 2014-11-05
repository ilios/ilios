<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\Offering;
use Mockery as m;

/**
 * Tests for Model Offering
 */
class OfferingTest extends BaseModel
{
    /**
     * @var Offering
     */
    protected $object;

    /**
     * Instantiate a Offering object
     */
    protected function setUp()
    {
        $this->object = new Offering;
    }

    /**
     * @covers Ilios\CoreBundle\Model\Offering::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getGroups());
        $this->assertEmpty($this->object->getInstructorGroups());
        $this->assertEmpty($this->object->getReccuringEvents());
        $this->assertEmpty($this->object->getUsers());
    }
    
    /**
     * @covers Ilios\CoreBundle\Model\Offering::getOfferingId
     */
    public function testGetOfferingId()
    {
        $this->basicGetTest('offeringId', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Offering::setRoom
     */
    public function testSetRoom()
    {
        $this->basicSetTest('room', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Offering::getRoom
     */
    public function testGetRoom()
    {
        $this->basicGetTest('room', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Offering::setStartDate
     */
    public function testSetStartDate()
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Offering::getStartDate
     */
    public function testGetStartDate()
    {
        $this->basicGetTest('startDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Offering::setEndDate
     */
    public function testSetEndDate()
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Offering::getEndDate
     */
    public function testGetEndDate()
    {
        $this->basicGetTest('endDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Offering::setDeleted
     */
    public function testSetDeleted()
    {
        $this->basicSetTest('deleted', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Offering::getDeleted
     */
    public function testGetDeleted()
    {
        $this->basicGetTest('deleted', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Offering::setLastUpdatedOn
     */
    public function testSetLastUpdatedOn()
    {
        $this->basicSetTest('lastUpdatedOn', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Offering::getLastUpdatedOn
     */
    public function testGetLastUpdatedOn()
    {
        $this->basicGetTest('lastUpdatedOn', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Offering::setSession
     */
    public function testSetSession()
    {
        $this->modelSetTest('session', 'Session');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Offering::getSession
     */
    public function testGetSession()
    {
        $this->modelGetTest('session', 'Session');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Offering::addGroup
     */
    public function testAddGroup()
    {
        $this->modelCollectionAddTest('group', 'Group');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Offering::removeGroup
     */
    public function testRemoveGroup()
    {
        $this->modelCollectionRemoveTest('group', 'Group');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Offering::getGroups
     */
    public function testGetGroups()
    {
        $this->modelCollectionGetTest('group', 'Group');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Offering::addInstructorGroup
     */
    public function testAddInstructorGroup()
    {
        $this->modelCollectionAddTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Offering::removeInstructorGroup
     */
    public function testRemoveInstructorGroup()
    {
        $this->modelCollectionRemoveTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Offering::getInstructorGroups
     */
    public function testGetInstructorGroups()
    {
        $this->modelCollectionGetTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Offering::addUser
     */
    public function testAddUser()
    {
        $this->modelCollectionAddTest('user', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Offering::removeUser
     */
    public function testRemoveUser()
    {
        $this->modelCollectionRemoveTest('user', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Offering::getUsers
     */
    public function testGetUsers()
    {
        $this->modelCollectionGetTest('user', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Offering::addReccuringEvent
     */
    public function testAddReccuringEvent()
    {
        $this->modelCollectionAddTest('reccuringEvent', 'RecurringEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Offering::removeReccuringEvent
     */
    public function testRemoveReccuringEvent()
    {
        $this->modelCollectionRemoveTest('reccuringEvent', 'RecurringEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Offering::getReccuringEvents
     */
    public function testGetReccuringEvents()
    {
        $this->modelCollectionGetTest('reccuringEvent', 'RecurringEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Offering::getPublishEvent
     */
    public function testGetPublishEvent()
    {
         $this->modelGetTest('publishEvent', 'PublishEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Offering::setPublishEvent
     */
    public function testSetPublishEvent()
    {
        $this->modelSetTest('publishEvent', 'PublishEvent');
    }
}
