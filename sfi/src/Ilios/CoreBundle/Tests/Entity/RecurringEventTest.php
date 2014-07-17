<?php
namespace Ilios\CoreBundle\Tests\Entity;


use Ilios\CoreBundle\Entity\RecurringEvent;
use Mockery as m;

/**
 * Tests for Entity RecurringEvent
 */
class RecurringEventTest extends EntityBase
{
    /**
     * @var RecurringEvent
     */
    protected $object;

    /**
     * Instantiate a RecurringEvent object
     */
    protected function setUp()
    {
        $this->object = new RecurringEvent;
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getOfferings());
    }
    

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::getRecurringEventId
     */
    public function testGetRecurringEventId()
    {
        $this->basicGetTest('recurringEventId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::setOnSunday
     */
    public function testSetOnSunday()
    {
        $this->basicSetTest('onSunday', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::getOnSunday
     */
    public function testGetOnSunday()
    {
        $this->basicGetTest('onSunday', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::setOnMonday
     */
    public function testSetOnMonday()
    {
        $this->basicSetTest('onMonday', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::getOnMonday
     */
    public function testGetOnMonday()
    {
        $this->basicGetTest('onMonday', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::setOnTuesday
     */
    public function testSetOnTuesday()
    {
        $this->basicSetTest('onTuesday', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::getOnTuesday
     */
    public function testGetOnTuesday()
    {
        $this->basicGetTest('onTuesday', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::setOnWednesday
     */
    public function testSetOnWednesday()
    {
        $this->basicSetTest('onWednesday', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::getOnWednesday
     */
    public function testGetOnWednesday()
    {
        $this->basicGetTest('onWednesday', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::setOnThursday
     */
    public function testSetOnThursday()
    {
        $this->basicSetTest('onThursday', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::getOnThursday
     */
    public function testGetOnThursday()
    {
        $this->basicGetTest('onThursday', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::setOnFriday
     */
    public function testSetOnFriday()
    {
        $this->basicSetTest('onFriday', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::getOnFriday
     */
    public function testGetOnFriday()
    {
        $this->basicGetTest('onFriday', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::setOnSaturday
     */
    public function testSetOnSaturday()
    {
        $this->basicSetTest('onSaturday', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::getOnSaturday
     */
    public function testGetOnSaturday()
    {
        $this->basicGetTest('onSaturday', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::setEndDate
     */
    public function testSetEndDate()
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::getEndDate
     */
    public function testGetEndDate()
    {
        $this->basicGetTest('endDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::setRepetitionCount
     */
    public function testSetRepetitionCount()
    {
        $this->basicSetTest('repetitionCount', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::getRepetitionCount
     */
    public function testGetRepetitionCount()
    {
        $this->basicGetTest('repetitionCount', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::setNextRecurringEvent
     */
    public function testSetNextRecurringEvent()
    {
        $this->entitySetTest('nextRecurringEvent', 'RecurringEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::getNextRecurringEvent
     */
    public function testGetNextRecurringEvent()
    {
        $this->entityGetTest('nextRecurringEvent', 'RecurringEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::setPreviousRecurringEvent
     */
    public function testSetPreviousRecurringEvent()
    {
        $this->entitySetTest('previousRecurringEvent', 'RecurringEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::getPreviousRecurringEvent
     */
    public function testGetPreviousRecurringEvent()
    {
        $this->entityGetTest('previousRecurringEvent', 'RecurringEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::addOffering
     */
    public function testAddOffering()
    {
        $this->entityCollectionAddTest('offering', 'Offering');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::removeOffering
     */
    public function testRemoveOffering()
    {
        $this->entityCollectionRemoveTest('offering', 'Offering');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::getOfferings
     */
    public function testGetOfferings()
    {
        $this->entityCollectionGetTest('offering', 'Offering');
    }
}
