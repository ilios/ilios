<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\School;
use App\Entity\SchoolConfig;
use Mockery as m;

/**
 * Tests for SchoolConfig entity.
 * @group model
 */
class SchoolConfigTest extends EntityBase
{
    /**
     * @var SchoolConfig
     */
    protected $object;

    /**
     * Instantiate a SchoolConfig object
     */
    protected function setUp(): void
    {
        $this->object = new SchoolConfig();
    }

    public function testNotBlankValidation()
    {
        $notBlank = [
            'name',
            'value'
        ];
        $this->object->setSchool(new School());
        $this->validateNotBlanks($notBlank);

        $this->object->setName('worstRoommate');
        $this->object->setValue('Jasper');
        $this->validate(0);
    }

    public function testNotNullValidation()
    {
        $this->object->setName('smallestDog');
        $this->object->setValue('Jayden');
        $notNull = [
            'school'
        ];
        $this->validateNotNulls($notNull);

        $this->object->setSchool(new School());
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\SchoolConfig::setName
     * @covers \App\Entity\SchoolConfig::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \App\Entity\SchoolConfig::setValue
     * @covers \App\Entity\SchoolConfig::getValue
     */
    public function testSetValue()
    {
        $this->basicSetTest('value', 'string');
    }

    /**
     * @covers \App\Entity\SchoolConfig::setValue
     * @covers \App\Entity\SchoolConfig::getValue
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }
}
