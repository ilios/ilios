<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Entity\SchoolInterface;
use App\Entity\SessionType;
use Exception;
use Mockery as m;

/**
 * Tests for Entity SessionType
 */
#[Group('model')]
#[CoversClass(SessionType::class)]
final class SessionTypeTest extends EntityBase
{
    protected SessionType $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new SessionType();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'title',
        ];
        $this->object->setSchool(m::mock(SchoolInterface::class));

        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('test');
        $this->validate(0);
    }

    public function testNotNullValidation(): void
    {
        $notNull = [
            'school',
        ];
        $this->object->setTitle('test');

        $this->validateNotNulls($notNull);

        $this->object->setSchool(m::mock(SchoolInterface::class));


        $this->validate(0);
    }
    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getAamcMethods());
        $this->assertCount(0, $this->object->getSessions());
    }

    public function testSetTitle(): void
    {
        $this->basicSetTest('title', 'string');
    }

    public function testSetSessionTypeCalendarColor(): void
    {
        $this->basicSetTest('calendarColor', 'hexcolor');
    }

    public function testIsActive(): void
    {
        $this->booleanSetTest('active');
    }

    public function testIsAssessment(): void
    {
        $this->booleanSetTest('assessment');
    }

    public function testSetAssessmentOption(): void
    {
        $this->entitySetTest('assessmentOption', 'AssessmentOption');
    }

    public function testAddAamcMethod(): void
    {
        $this->entityCollectionAddTest('aamcMethod', 'AamcMethod');
    }

    public function testRemoveAamcMethod(): void
    {
        $this->entityCollectionRemoveTest('aamcMethod', 'AamcMethod');
    }

    public function testSetAamcMethods(): void
    {
        $this->entityCollectionSetTest('aamcMethod', 'AamcMethod');
    }

    public function testAddSession(): void
    {
        $this->entityCollectionAddTest('session', 'Session', false, false, 'setSessionType');
    }

    public function testRemoveSession(): void
    {
        $this->expectException(Exception::class);
        $this->entityCollectionRemoveTest('session', 'Session');
    }

    public function testSetSessions(): void
    {
        $this->entityCollectionSetTest('session', 'Session', false, false, 'setSessionType');
    }

    public function testValidHexCodes(): void
    {
        $this->object->setTitle('test');
        $this->object->setSchool(m::mock(SchoolInterface::class));
        $this->object->setCalendarColor('#123abc');
        $this->validate(0);

        $this->object->setCalendarColor('#111AaA');
        $this->validate(0);

        $this->object->setCalendarColor('#000000');
        $this->validate(0);

        $this->object->setCalendarColor('#ffffff');
        $this->validate(0);

        $this->object->setCalendarColor('#ABCDEF');
        $this->validate(0);

        $this->object->setCalendarColor('123');
        $this->validate(1);

        $this->object->setCalendarColor('123abc');
        $this->validate(1);

        $this->object->setCalendarColor('#fff');
        $this->validate(1);

        $this->object->setCalendarColor('#ff');
        $this->validate(1);

        $this->object->setCalendarColor('#0');
        $this->validate(1);
    }

    protected function getObject(): SessionType
    {
        return $this->object;
    }
}
