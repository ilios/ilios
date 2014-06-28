<?php
namespace Ilios\CoreBundle\Tests\Entity;


use Ilios\CoreBundle\Entity\PublishEvent;
use Mockery as m;

/**
 * Tests for Entity PublishEvent
 */
class PublishEventTest extends EntityBase
{
    /**
     * @var PublishEvent
     */
    protected $object;

    /**
     * Instantiate a PublishEvent object
     */
    protected function setUp()
    {
        $this->object = new PublishEvent;
    }
    

    /**
     * @covers Ilios\CoreBundle\Entity\PublishEvent::getPublishEventId
     */
    public function testGetPublishEventId()
    {
        $this->basicGetTest('publishEventId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\PublishEvent::setMachineIp
     */
    public function testSetMachineIp()
    {
        $this->basicSetTest('machineIp', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\PublishEvent::getMachineIp
     */
    public function testGetMachineIp()
    {
        $this->basicGetTest('machineIp', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\PublishEvent::setTimeStamp
     */
    public function testSetTimeStamp()
    {
        $this->basicSetTest('timeStamp', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\PublishEvent::getTimeStamp
     */
    public function testGetTimeStamp()
    {
        $this->basicGetTest('timeStamp', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\PublishEvent::setTableName
     */
    public function testSetTableName()
    {
        $this->basicSetTest('tableName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\PublishEvent::getTableName
     */
    public function testGetTableName()
    {
        $this->basicGetTest('tableName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\PublishEvent::setTableRowId
     */
    public function testSetTableRowId()
    {
        $this->basicSetTest('tableRowId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\PublishEvent::getTableRowId
     */
    public function testGetTableRowId()
    {
        $this->basicGetTest('tableRowId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\PublishEvent::setAdministrator
     */
    public function testSetAdministrator()
    {
        $this->entitySetTest('administrator', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\PublishEvent::getAdministrator
     */
    public function testGetAdministrator()
    {
        $this->entityGetTest('administrator', 'User');
    }
}
