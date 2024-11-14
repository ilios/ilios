<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\AlertChangeType;

/**
 * Tests for Entity AlertChangeType
 * @group model
 */
class AlertChangeTypeTest extends EntityBase
{
    protected AlertChangeType $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new AlertChangeType();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'title',
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
        $this->assertCount(0, $this->object->getAlerts());
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

    protected function getObject(): AlertChangeType
    {
        return $this->object;
    }
}
