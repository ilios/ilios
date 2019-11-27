<?php
namespace App\Tests\Entity;

use App\Entity\AlertChangeType;
use Mockery as m;

/**
 * Tests for Entity AlertChangeType
 * @group model
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
     * @covers \App\Entity\AlertChangeType::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getAlerts());
    }

    /**
     * @covers \App\Entity\AlertChangeType::setTitle
     * @covers \App\Entity\AlertChangeType::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\AlertChangeType::addAlert
     */
    public function testAddAlert()
    {
        $this->entityCollectionAddTest('alert', 'Alert', false, false, 'addChangeType');
    }

    /**
     * @covers \App\Entity\AlertChangeType::removeAlert
     */
    public function testRemoveAlert()
    {
        $this->entityCollectionRemoveTest('alert', 'Alert', false, false, false, 'removeChangeType');
    }

    /**
     * @covers \App\Entity\AlertChangeType::getAlerts
     */
    public function testGetAlerts()
    {
        $this->entityCollectionSetTest('alert', 'Alert', false, false, 'addChangeType');
    }
}
