<?php
namespace Tests\CoreBundle\Entity;

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

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'title'
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('Title it is');
        $this->validate(0);
    }
    /**
     * @covers \Ilios\CoreBundle\Entity\AlertChangeType::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getAlerts());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\AlertChangeType::setTitle
     * @covers \Ilios\CoreBundle\Entity\AlertChangeType::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\AlertChangeType::addAlert
     */
    public function testAddAlert()
    {
        $this->entityCollectionAddTest('alert', 'Alert', false, false, 'addChangeType');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\AlertChangeType::removeAlert
     */
    public function testRemoveAlert()
    {
        $this->entityCollectionRemoveTest('alert', 'Alert', false, false, false, 'removeChangeType');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\AlertChangeType::getAlerts
     */
    public function testGetAlerts()
    {
        $this->entityCollectionSetTest('alert', 'Alert', false, false, 'addChangeType');
    }
}
