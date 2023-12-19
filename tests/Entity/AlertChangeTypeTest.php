<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\AlertChangeType;
use Mockery as m;

/**
 * Tests for Entity AlertChangeType
 * @group model
 */
class AlertChangeTypeTest extends EntityBase
{
    protected function setUp(): void
    {
        $this->object = new AlertChangeType();
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'title'
        ];
        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('Title it is');
        $this->validate(0);
    }
    /**
     * @covers \App\Entity\AlertChangeType::__construct
     */
    public function testConstructor(): void
    {
        $this->assertEmpty($this->object->getAlerts());
    }

    /**
     * @covers \App\Entity\AlertChangeType::setTitle
     * @covers \App\Entity\AlertChangeType::getTitle
     */
    public function testSetTitle(): void
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\AlertChangeType::addAlert
     */
    public function testAddAlert(): void
    {
        $this->entityCollectionAddTest('alert', 'Alert', false, false, 'addChangeType');
    }

    /**
     * @covers \App\Entity\AlertChangeType::removeAlert
     */
    public function testRemoveAlert(): void
    {
        $this->entityCollectionRemoveTest('alert', 'Alert', false, false, false, 'removeChangeType');
    }

    /**
     * @covers \App\Entity\AlertChangeType::getAlerts
     */
    public function testGetAlerts(): void
    {
        $this->entityCollectionSetTest('alert', 'Alert', false, false, 'addChangeType');
    }
}
