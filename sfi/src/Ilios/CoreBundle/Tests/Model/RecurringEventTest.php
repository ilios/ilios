<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\RecurringEvent;
use Mockery as m;

/**
 * Tests for Model RecurringEvent
 */
class RecurringEventTest extends ModelBase
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
     * @covers Ilios\CoreBundle\Model\RecurringEvent::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getOfferings());
    }
    

    /**
     * @covers Ilios\CoreBundle\Model\RecurringEvent::getRecurringEventId
     */
    public function testGetRecurringEventId()
    {
        $this->basicGetTest('recurringEventId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\RecurringEvent::setOnSunday
     */
    public function testSetOnSunday()
    {
        $this->basicSetTest('onSunday', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\RecurringEvent::getOnSunday
     */
    public function testGetOnSunday()
    {
        $this->basicGetTest('onSunday', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\RecurringEvent::setOnMonday
     */
    public function testSetOnMonday()
    {
        $this->basicSetTest('onMonday', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\RecurringEvent::getOnMonday
     */
    public function testGetOnMonday()
    {
        $this->basicGetTest('onMonday', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\RecurringEvent::setOnTuesday
     */
    public function testSetOnTuesday()
    {
        $this->basicSetTest('onTuesday', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\RecurringEvent::getOnTuesday
     */
    public function testGetOnTuesday()
    {
        $this->basicGetTest('onTuesday', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\RecurringEvent::setOnWednesday
     */
    public function testSetOnWednesday()
    {
        $this->basicSetTest('onWednesday', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\RecurringEvent::getOnWednesday
     */
    public function testGetOnWednesday()
    {
        $this->basicGetTest('onWednesday', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\RecurringEvent::setOnThursday
     */
    public function testSetOnThursday()
    {
        $this->basicSetTest('onThursday', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\RecurringEvent::getOnThursday
     */
    public function testGetOnThursday()
    {
        $this->basicGetTest('onThursday', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\RecurringEvent::setOnFriday
     */
    public function testSetOnFriday()
    {
        $this->basicSetTest('onFriday', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\RecurringEvent::getOnFriday
     */
    public function testGetOnFriday()
    {
        $this->basicGetTest('onFriday', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\RecurringEvent::setOnSaturday
     */
    public function testSetOnSaturday()
    {
        $this->basicSetTest('onSaturday', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\RecurringEvent::getOnSaturday
     */
    public function testGetOnSaturday()
    {
        $this->basicGetTest('onSaturday', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\RecurringEvent::setEndDate
     */
    public function testSetEndDate()
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\RecurringEvent::getEndDate
     */
    public function testGetEndDate()
    {
        $this->basicGetTest('endDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\RecurringEvent::setRepetitionCount
     */
    public function testSetRepetitionCount()
    {
        $this->basicSetTest('repetitionCount', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\RecurringEvent::getRepetitionCount
     */
    public function testGetRepetitionCount()
    {
        $this->basicGetTest('repetitionCount', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\RecurringEvent::setNextRecurringEvent
     */
    public function testSetNextRecurringEvent()
    {
        $this->modelSetTest('nextRecurringEvent', 'RecurringEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Model\RecurringEvent::getNextRecurringEvent
     */
    public function testGetNextRecurringEvent()
    {
        $this->modelGetTest('nextRecurringEvent', 'RecurringEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Model\RecurringEvent::setPreviousRecurringEvent
     */
    public function testSetPreviousRecurringEvent()
    {
        $this->modelSetTest('previousRecurringEvent', 'RecurringEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Model\RecurringEvent::getPreviousRecurringEvent
     */
    public function testGetPreviousRecurringEvent()
    {
        $this->modelGetTest('previousRecurringEvent', 'RecurringEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Model\RecurringEvent::addOffering
     */
    public function testAddOffering()
    {
        $this->modelCollectionAddTest('offering', 'Offering');
    }

    /**
     * @covers Ilios\CoreBundle\Model\RecurringEvent::removeOffering
     */
    public function testRemoveOffering()
    {
        $this->modelCollectionRemoveTest('offering', 'Offering');
    }

    /**
     * @covers Ilios\CoreBundle\Model\RecurringEvent::getOfferings
     */
    public function testGetOfferings()
    {
        $this->modelCollectionGetTest('offering', 'Offering');
    }
}
