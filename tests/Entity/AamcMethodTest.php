<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\AamcMethod;

/**
 * Tests for Entity AamcMethod
 * @group model
 */
class AamcMethodTest extends EntityBase
{
    protected function setUp(): void
    {
        $this->object = new AamcMethod();
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'id',
            'description'
        ];
        $this->validateNotBlanks($notBlank);

        $this->object->setId('strtest');
        $this->object->setDescription('my car is great');
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\AamcMethod::__construct
     */
    public function testConstructor(): void
    {
        $this->assertEmpty($this->object->getSessionTypes());
    }

    /**
     * @covers \App\Entity\AamcMethod::setDescription
     * @covers \App\Entity\AamcMethod::getDescription
     */
    public function testSetDescription(): void
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers \App\Entity\AamcMethod::addSessionType
     */
    public function testAddSessionType(): void
    {
        $this->entityCollectionAddTest('sessionType', 'SessionType', false, false, 'addAamcMethod');
    }

    /**
     * @covers \App\Entity\AamcMethod::removeSessionType
     */
    public function testRemoveSessionType(): void
    {
        $this->entityCollectionRemoveTest('sessionType', 'SessionType', false, false, false, 'removeAamcMethod');
    }

    /**
     * @covers \App\Entity\AamcMethod::getSessionTypes
     */
    public function testGetSessionTypes(): void
    {
        $this->entityCollectionSetTest('sessionType', 'SessionType', false, false, 'addAamcMethod');
    }

    /**
     * @covers \App\Entity\AamcMethod::setActive
     * @covers \App\Entity\AamcMethod::isActive
     */
    public function testIsActive(): void
    {
        $this->booleanSetTest('active');
    }
}
