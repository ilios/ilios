<?php
namespace Ilios\CoreBundle\Tests\Entity;


use Ilios\CoreBundle\Entity\Offering;
use Mockery as m;

/**
 * Tests for Entity Offering
 */
class OfferingTest extends EntityBase
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
     * @covers Ilios\CoreBundle\Entity\Offering::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getGroups());
        $this->assertEmpty($this->object->getInstructorGroups());
        $this->assertEmpty($this->object->getReccuringEvents());
        $this->assertEmpty($this->object->getUsers());
    }
    
    /**
     * @covers Ilios\CoreBundle\Entity\Offering::getOfferingId
     */
    public function testGetOfferingId()
    {
        $this->basicGetTest('offeringId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::setRoom
     */
    public function testSetRoom()
    {
        $this->basicSetTest('room', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::getRoom
     */
    public function testGetRoom()
    {
        $this->basicGetTest('room', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::setStartDate
     */
    public function testSetStartDate()
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::getStartDate
     */
    public function testGetStartDate()
    {
        $this->basicGetTest('startDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::setEndDate
     */
    public function testSetEndDate()
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::getEndDate
     */
    public function testGetEndDate()
    {
        $this->basicGetTest('endDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::setDeleted
     */
    public function testSetDeleted()
    {
        $this->basicSetTest('deleted', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::getDeleted
     */
    public function testGetDeleted()
    {
        $this->basicGetTest('deleted', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::setLastUpdatedOn
     */
    public function testSetLastUpdatedOn()
    {
        $this->basicSetTest('lastUpdatedOn', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::getLastUpdatedOn
     */
    public function testGetLastUpdatedOn()
    {
        $this->basicGetTest('lastUpdatedOn', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::setSession
     */
    public function testSetSession()
    {
        $this->entitySetTest('session', 'Session');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::getSession
     */
    public function testGetSession()
    {
        $this->entityGetTest('session', 'Session');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::addGroup
     */
    public function testAddGroup()
    {
        $this->entityCollectionAddTest('group', 'Group');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::removeGroup
     */
    public function testRemoveGroup()
    {
        $this->entityCollectionRemoveTest('group', 'Group');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::getGroups
     */
    public function testGetGroups()
    {
        $this->entityCollectionGetTest('group', 'Group');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::addInstructorGroup
     */
    public function testAddInstructorGroup()
    {
        $this->entityCollectionAddTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::removeInstructorGroup
     */
    public function testRemoveInstructorGroup()
    {
        $this->entityCollectionRemoveTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::getInstructorGroups
     */
    public function testGetInstructorGroups()
    {
        $this->entityCollectionGetTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::addUser
     */
    public function testAddUser()
    {
        $this->entityCollectionAddTest('user', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::removeUser
     */
    public function testRemoveUser()
    {
        $this->entityCollectionRemoveTest('user', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::getUsers
     */
    public function testGetUsers()
    {
        $this->entityCollectionGetTest('user', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::addReccuringEvent
     */
    public function testAddReccuringEvent()
    {
        $this->entityCollectionAddTest('reccuringEvent', 'RecurringEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::removeReccuringEvent
     */
    public function testRemoveReccuringEvent()
    {
        $this->entityCollectionRemoveTest('reccuringEvent', 'RecurringEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::getReccuringEvents
     */
    public function testGetReccuringEvents()
    {
        $this->entityCollectionGetTest('reccuringEvent', 'RecurringEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::getPublishEvent
     */
    public function testGetPublishEvent()
    {
         $this->entityGetTest('publishEvent', 'PublishEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::setPublishEvent
     */
    public function testSetPublishEvent()
    {
        $this->entitySetTest('publishEvent', 'PublishEvent');
    }
}
