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


    // fixed all of the Weekday fields to NotNull()
    // now there are no NotBlank() fields to validate so no test will be put in here

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getOfferings());
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::setOnSunday
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::isOnSunday
     */
    public function testSetOnSunday()
    {
        $this->booleanSetTest('onSunday');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::setOnMonday
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::isOnMonday
     */
    public function testSetOnMonday()
    {
        $this->booleanSetTest('onMonday');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::setOnTuesday
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::isOnTuesday
     */
    public function testSetOnTuesday()
    {
        $this->booleanSetTest('onTuesday');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::setOnWednesday
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::isOnWednesday
     */
    public function testSetOnWednesday()
    {
        $this->booleanSetTest('onWednesday');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::setOnThursday
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::isOnThursday
     */
    public function testSetOnThursday()
    {
        $this->booleanSetTest('onThursday');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::setOnFriday
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::isOnFriday
     */
    public function testSetOnFriday()
    {
        $this->booleanSetTest('onFriday');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::setOnSaturday
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::isOnSaturday
     */
    public function testSetOnSaturday()
    {
        $this->booleanSetTest('onSaturday');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::setEndDate
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::getEndDate
     */
    public function testSetEndDate()
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::setRepetitionCount
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::getRepetitionCount
     */
    public function testSetRepetitionCount()
    {
        $this->basicSetTest('repetitionCount', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::setNextRecurringEvent
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::getNextRecurringEvent
     */
    public function testSetNextRecurringEvent()
    {
        $this->entitySetTest('nextRecurringEvent', 'RecurringEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::setPreviousRecurringEvent
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::getPreviousRecurringEvent
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
     * @covers Ilios\CoreBundle\Entity\RecurringEvent::getOfferings
     */
    public function testGetOfferings()
    {
        $this->entityCollectionSetTest('offering', 'Offering');
    }
}
