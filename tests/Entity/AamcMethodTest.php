<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Entity\AamcMethod;

/**
 * Tests for Entity AamcMethod
 */
#[Group('model')]
#[CoversClass(AamcMethod::class)]
final class AamcMethodTest extends EntityBase
{
    protected AamcMethod $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new AamcMethod();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'id',
            'description',
        ];
        $this->validateNotBlanks($notBlank);

        $this->object->setId('strtest');
        $this->object->setDescription('my car is great');
        $this->validate(0);
    }

    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getSessionTypes());
    }

    public function testSetDescription(): void
    {
        $this->basicSetTest('description', 'string');
    }

    public function testAddSessionType(): void
    {
        $this->entityCollectionAddTest('sessionType', 'SessionType', false, false, 'addAamcMethod');
    }

    public function testRemoveSessionType(): void
    {
        $this->entityCollectionRemoveTest('sessionType', 'SessionType', false, false, false, 'removeAamcMethod');
    }

    public function testGetSessionTypes(): void
    {
        $this->entityCollectionSetTest('sessionType', 'SessionType', false, false, 'addAamcMethod');
    }

    public function testIsActive(): void
    {
        $this->booleanSetTest('active');
    }

    protected function getObject(): AamcMethod
    {
        return $this->object;
    }
}
