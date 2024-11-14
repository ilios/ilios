<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\AssessmentOption;

/**
 * Tests for Entity AssessmentOption
 * @group model
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Entity\AssessmentOption::class)]
class AssessmentOptionTest extends EntityBase
{
    protected AssessmentOption $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new AssessmentOption();
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
        ];
        $this->validateNotBlanks($notBlank);

        $this->object->setName('Smorgasbord');
        $this->validate(0);
    }

    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getSessionTypes());
    }

    public function testSetName(): void
    {
        $this->basicSetTest('name', 'string');
    }

    public function testAddSessionType(): void
    {
        $this->entityCollectionAddTest('sessionType', 'SessionType');
    }

    public function testRemoveSessionType(): void
    {
        $this->entityCollectionRemoveTest('sessionType', 'SessionType');
    }

    public function testGetSessionTypes(): void
    {
        $this->entityCollectionSetTest('sessionType', 'SessionType');
    }

    protected function getObject(): AssessmentOption
    {
        return $this->object;
    }
}
