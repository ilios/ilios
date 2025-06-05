<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Entity\ApplicationConfig;

/**
 * Tests for ApplicationConfig entity.
 */
#[Group('model')]
#[CoversClass(ApplicationConfig::class)]
final class ApplicationConfigTest extends EntityBase
{
    protected ApplicationConfig $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new ApplicationConfig();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'name',
            'value',
        ];
        $this->validateNotBlanks($notBlank);

        $this->object->setName('bestDog');
        $this->object->setValue('jackson');
        $this->validate(0);
    }

    public function testSetName(): void
    {
        $this->basicSetTest('name', 'string');
    }

    public function testSetValue(): void
    {
        $this->basicSetTest('value', 'string');
    }

    protected function getObject(): ApplicationConfig
    {
        return $this->object;
    }
}
