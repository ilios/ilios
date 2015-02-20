<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\AlertChangeType;
use Mockery as m;

/**
 * Tests for Entity AlertChangeType
 */
class AlertChangeTypeTest extends EntityBase
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
     * @covers Ilios\CoreBundle\Entity\AlertChangeType::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getAlerts());
    }

    /**
     * @covers Ilios\CoreBundle\Entity\AlertChangeType::setTitle
     * @covers Ilios\CoreBundle\Entity\AlertChangeType::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\AlertChangeType::addAlert
     */
    public function testAddAlert()
    {
        $this->entityCollectionAddTest('alert', 'Alert');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\AlertChangeType::getAlerts
     */
    public function testGetAlerts()
    {
        $this->entityCollectionSetTest('alert', 'Alert');
    }
}
