<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\SchoolInterface;
use App\Entity\SessionType;
use Exception;
use Mockery as m;

/**
 * Tests for Entity SessionType
 * @group model
 */
class SessionTypeTest extends EntityBase
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
    /**
     * @covers \App\Entity\SessionType::__construct
     */
    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getAamcMethods());
        $this->assertCount(0, $this->object->getSessions());
    }

    /**
     * @covers \App\Entity\SessionType::setTitle
     * @covers \App\Entity\SessionType::getTitle
     */
    public function testSetTitle(): void
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\SessionType::setCalendarColor()
     * @covers \App\Entity\SessionType::getCalendarColor()
     */
    public function testSetSessionTypeCalendarColor(): void
    {
        $this->basicSetTest('calendarColor', 'hexcolor');
    }

    /**
     * @covers \App\Entity\SessionType::setActive
     * @covers \App\Entity\SessionType::isActive
     */
    public function testIsActive(): void
    {
        $this->booleanSetTest('active');
    }

    /**
     * @covers \App\Entity\SessionType::setAssessment
     * @covers \App\Entity\SessionType::isAssessment
     */
    public function testIsAssessment(): void
    {
        $this->booleanSetTest('assessment');
    }

    /**
     * @covers \App\Entity\SessionType::setAssessmentOption
     * @covers \App\Entity\SessionType::getAssessmentOption
     */
    public function testSetAssessmentOption(): void
    {
        $this->entitySetTest('assessmentOption', 'AssessmentOption');
    }

    /**
     * @covers \App\Entity\SessionType::addAamcMethod
     */
    public function testAddAamcMethod(): void
    {
        $this->entityCollectionAddTest('aamcMethod', 'AamcMethod');
    }

    /**
     * @covers \App\Entity\SessionType::removeAamcMethod
     */
    public function testRemoveAamcMethod(): void
    {
        $this->entityCollectionRemoveTest('aamcMethod', 'AamcMethod');
    }

    /**
     * @covers \App\Entity\SessionType::setAamcMethods
     * @covers \App\Entity\SessionType::getAamcMethods
     */
    public function testSetAamcMethods(): void
    {
        $this->entityCollectionSetTest('aamcMethod', 'AamcMethod');
    }

    /**
     * @covers \App\Entity\SessionType::addSession
     */
    public function testAddSession(): void
    {
        $this->entityCollectionAddTest('session', 'Session', false, false, 'setSessionType');
    }

    /**
     * @covers \App\Entity\SessionType::removeSession
     */
    public function testRemoveSession(): void
    {
        $this->expectException(Exception::class);
        $this->entityCollectionRemoveTest('session', 'Session');
    }

    /**
     * @covers \App\Entity\SessionType::setSessions
     */
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
