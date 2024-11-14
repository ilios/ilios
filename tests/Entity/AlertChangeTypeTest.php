<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\AlertChangeType;

/**
 * Tests for Entity AlertChangeType
 * @group model
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Entity\AlertChangeType::class)]
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
    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getAlerts());
    }

    public function testSetTitle(): void
    {
        $this->basicSetTest('title', 'string');
    }

    public function testAddAlert(): void
    {
        $this->entityCollectionAddTest('alert', 'Alert', false, false, 'addChangeType');
    }

    public function testRemoveAlert(): void
    {
        $this->entityCollectionRemoveTest('alert', 'Alert', false, false, false, 'removeChangeType');
    }

    public function testGetAlerts(): void
    {
        $this->entityCollectionSetTest('alert', 'Alert', false, false, 'addChangeType');
    }

    protected function getObject(): AlertChangeType
    {
        return $this->object;
    }
}
