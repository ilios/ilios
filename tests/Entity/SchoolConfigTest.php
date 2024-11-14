<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\School;
use App\Entity\SchoolConfig;

/**
 * Tests for SchoolConfig entity.
 */
#[\PHPUnit\Framework\Attributes\Group('model')]
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Entity\SchoolConfig::class)]
class SchoolConfigTest extends EntityBase
{
    protected SchoolConfig $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new SchoolConfig();
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
        $this->object->setSchool(new School());
        $this->validateNotBlanks($notBlank);

        $this->object->setName('worstRoommate');
        $this->object->setValue('Jasper');
        $this->validate(0);
    }

    public function testNotNullValidation(): void
    {
        $this->object->setName('smallestDog');
        $this->object->setValue('Jayden');
        $notNull = [
            'school',
        ];
        $this->validateNotNulls($notNull);

        $this->object->setSchool(new School());
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

    public function testSetSchool(): void
    {
        $this->entitySetTest('school', 'School');
    }

    protected function getObject(): SchoolConfig
    {
        return $this->object;
    }
}
