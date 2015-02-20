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
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::setOnSunday
     */
    public function testSetOnSunday()
    {
        $this->booleanSetTest('onSunday');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::setOnMonday
     */
    public function testSetOnMonday()
    {
        $this->booleanSetTest('onMonday');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::setOnTuesday
     */
    public function testSetOnTuesday()
    {
        $this->booleanSetTest('onTuesday');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::setOnWednesday
     */
    public function testSetOnWednesday()
    {
        $this->booleanSetTest('onWednesday');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::setOnThursday
     */
    public function testSetOnThursday()
    {
        $this->booleanSetTest('onThursday');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::setOnFriday
     */
    public function testSetOnFriday()
    {
        $this->booleanSetTest('onFriday');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::setOnSaturday
     */
    public function testSetOnSaturday()
    {
        $this->booleanSetTest('onSaturday');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::setEndDate
     */
    public function testSetEndDate()
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::setRepetitionCount
     */
    public function testSetRepetitionCount()
    {
        $this->basicSetTest('repetitionCount', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::setNextRecurringEvent
     */
    public function testSetNextRecurringEvent()
    {
        $this->entitySetTest('nextRecurringEvent', 'RecurringEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::setPreviousRecurringEvent
     */
    public function testSetPreviousRecurringEvent()
    {
        $this->entitySetTest('previousRecurringEvent', 'RecurringEvent');
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
        $this->entityCollectionSetTest('offering', 'Offering');
    }
}
