<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\ApplicationConfig;
use Mockery as m;

/**
 * Tests for ApplicationConfig entity.
 * @group model
 */
class ApplicationConfigTest extends EntityBase
{
    /**
     * @var ApplicationConfig
     */
    protected $object;

    /**
     * Instantiate a ApplicationConfig object
     */
    protected function setUp(): void
    {
        $this->object = new ApplicationConfig();
    }

    public function testNotBlankValidation()
    {
        $notBlank = [
            'name',
            'value'
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
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \App\Entity\ApplicationConfig::setValue
     * @covers \App\Entity\ApplicationConfig::getValue
     */
    public function testSetValue()
    {
        $this->basicSetTest('value', 'string');
    }
}
