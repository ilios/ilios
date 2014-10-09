<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\PublishEvent;
use Mockery as m;

/**
 * Tests for Model PublishEvent
 */
class PublishEventTest extends ModelBase
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
     * @covers Ilios\CoreBundle\Model\PublishEvent::getPublishEventId
     */
    public function testGetPublishEventId()
    {
        $this->basicGetTest('publishEventId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\PublishEvent::setMachineIp
     */
    public function testSetMachineIp()
    {
        $this->basicSetTest('machineIp', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\PublishEvent::getMachineIp
     */
    public function testGetMachineIp()
    {
        $this->basicGetTest('machineIp', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\PublishEvent::setTimeStamp
     */
    public function testSetTimeStamp()
    {
        $this->basicSetTest('timeStamp', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\PublishEvent::getTimeStamp
     */
    public function testGetTimeStamp()
    {
        $this->basicGetTest('timeStamp', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\PublishEvent::setTableName
     */
    public function testSetTableName()
    {
        $this->basicSetTest('tableName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\PublishEvent::getTableName
     */
    public function testGetTableName()
    {
        $this->basicGetTest('tableName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\PublishEvent::setTableRowId
     */
    public function testSetTableRowId()
    {
        $this->basicSetTest('tableRowId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\PublishEvent::getTableRowId
     */
    public function testGetTableRowId()
    {
        $this->basicGetTest('tableRowId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\PublishEvent::setAdministrator
     */
    public function testSetAdministrator()
    {
        $this->modelSetTest('administrator', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\PublishEvent::getAdministrator
     */
    public function testGetAdministrator()
    {
        $this->modelGetTest('administrator', 'User');
    }
}
