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
        $this->assertEmpty($this->object->getLearnerGroups());
        $this->assertEmpty($this->object->getInstructorGroups());
        $this->assertEmpty($this->object->getRecurringEvents());
        $this->assertEmpty($this->object->getUsers());
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::setRoom
     */
    public function testSetRoom()
    {
        $this->basicSetTest('room', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::setStartDate
     */
    public function testSetStartDate()
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::setEndDate
     */
    public function testSetEndDate()
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::setDeleted
     */
    public function testSetDeleted()
    {
        $this->booleanSetTest('deleted');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::setLastUpdatedOn
     */
    public function testSetLastUpdatedOn()
    {
        $this->basicSetTest('lastUpdatedOn', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::setSession
     */
    public function testSetSession()
    {
        $this->entitySetTest('session', 'Session');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::addLearnerGroup
     */
    public function testAddLearnerGroup()
    {
        $this->entityCollectionAddTest('learnerGroup', 'LearnerGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::removeLearnerGroup
     */
    public function testRemoveLearnerGroup()
    {
        $this->entityCollectionRemoveTest('learnerGroup', 'LearnerGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::setPublishEvent
     */
    public function testSetPublishEvent()
    {
        $this->entitySetTest('publishEvent', 'PublishEvent');
    }
}
