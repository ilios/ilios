<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\AssessmentOption;

/**
 * Tests for Entity AssessmentOption
 * @group model
 */
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

    /**
     * @covers \App\Entity\AssessmentOption::__construct
     */
    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getSessionTypes());
    }

    /**
     * @covers \App\Entity\AssessmentOption::setName
     * @covers \App\Entity\AssessmentOption::getName
     */
    public function testSetName(): void
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \App\Entity\AssessmentOption::addSessionType
     */
    public function testAddSessionType(): void
    {
        $this->entityCollectionAddTest('sessionType', 'SessionType');
    }

    /**
     * @covers \App\Entity\AssessmentOption::removeSessionType
     */
    public function testRemoveSessionType(): void
    {
        $this->entityCollectionRemoveTest('sessionType', 'SessionType');
    }

    /**
     * @covers \App\Entity\AssessmentOption::getSessionTypes
     */
    public function testGetSessionTypes(): void
    {
        $this->entityCollectionSetTest('sessionType', 'SessionType');
    }

    protected function getObject(): AssessmentOption
    {
        return $this->object;
    }
}
