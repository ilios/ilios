<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\ApplicationConfig;

/**
 * Tests for ApplicationConfig entity.
 * @group model
 */
class ApplicationConfigTest extends EntityBase
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

    /**
     * @covers \App\Entity\ApplicationConfig::setName
     * @covers \App\Entity\ApplicationConfig::getName
     */
    public function testSetName(): void
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \App\Entity\ApplicationConfig::setValue
     * @covers \App\Entity\ApplicationConfig::getValue
     */
    public function testSetValue(): void
    {
        $this->basicSetTest('value', 'string');
    }

    protected function getObject(): ApplicationConfig
    {
        return $this->object;
    }
}
