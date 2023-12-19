<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\AssessmentOption;
use Mockery as m;

/**
 * Tests for Entity AssessmentOption
 * @group model
 */
class AssessmentOptionTest extends EntityBase
{
    protected function setUp(): void
    {
        $this->object = new AssessmentOption();
    }


    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'name'
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
        $this->assertEmpty($this->object->getSessionTypes());
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
}
