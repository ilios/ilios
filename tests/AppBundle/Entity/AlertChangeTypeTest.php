<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\AlertChangeType;
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
     * @covers \AppBundle\Entity\AlertChangeType::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getAlerts());
    }

    /**
     * @covers \AppBundle\Entity\AlertChangeType::setTitle
     * @covers \AppBundle\Entity\AlertChangeType::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \AppBundle\Entity\AlertChangeType::addAlert
     */
    public function testAddAlert()
    {
        $this->entityCollectionAddTest('alert', 'Alert', false, false, 'addChangeType');
    }

    /**
     * @covers \AppBundle\Entity\AlertChangeType::removeAlert
     */
    public function testRemoveAlert()
    {
        $this->entityCollectionRemoveTest('alert', 'Alert', false, false, false, 'removeChangeType');
    }

    /**
     * @covers \AppBundle\Entity\AlertChangeType::getAlerts
     */
    public function testGetAlerts()
    {
        $this->entityCollectionSetTest('alert', 'Alert', false, false, 'addChangeType');
    }
}
