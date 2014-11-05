<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\AlertChangeType;
use Mockery as m;

/**
 * Tests for Model AlertChangeType
 */
class AlertChangeTypeTest extends BaseModel
{
    /**
     * @var AlertChangeType
     */
    protected $object;

    /**
     * Instantiate a AlertChangeType object
     */
    protected function setUp()
    {
        $this->object = new AlertChangeType;
    }

    /**
     * @covers Ilios\CoreBundle\Model\AlertChangeType::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getAlerts());
    }
    
    /**
     * @covers Ilios\CoreBundle\Model\AlertChangeType::getAlertChangeTypeId
     */
    public function testGetAlertChangeTypeId()
    {
        $this->basicGetTest('alertChangeTypeId', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\AlertChangeType::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\AlertChangeType::getTitle
     */
    public function testGetTitle()
    {
        $this->basicGetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\AlertChangeType::addAlert
     */
    public function testAddAlert()
    {
        $this->modelCollectionAddTest('alert', 'Alert');
    }

    /**
     * @covers Ilios\CoreBundle\Model\AlertChangeType::removeAlert
     */
    public function testRemoveAlert()
    {
        $this->modelCollectionRemoveTest('alert', 'Alert');
    }

    /**
     * @covers Ilios\CoreBundle\Model\AlertChangeType::getAlerts
     */
    public function testGetAlerts()
    {
        $this->modelCollectionGetTest('alert', 'Alert');
    }
}
