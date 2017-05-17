<?php
namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\SessionType;
use Mockery as m;

/**
 * Tests for Entity SessionType
 */
class SessionTypeTest extends EntityBase
{
    /**
     * @var SessionType
     */
    protected $object;

    /**
     * Instantiate a SessionType object
     */
    protected function setUp()
    {
        $this->object = new SessionType;
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'title'
        );
        $this->object->setSchool(m::mock('Ilios\CoreBundle\Entity\SchoolInterface'));

        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('test');
        $this->validate(0);
    }

    public function testNotNullValidation()
    {
        $notNull = array(
            'school'
        );
        $this->object->setTitle('test');

        $this->validateNotNulls($notNull);

        $this->object->setSchool(m::mock('Ilios\CoreBundle\Entity\SchoolInterface'));


        $this->validate(0);
    }
    /**
     * @covers \Ilios\CoreBundle\Entity\SessionType::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getAamcMethods());
        $this->assertEmpty($this->object->getSessions());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\SessionType::setTitle
     * @covers \Ilios\CoreBundle\Entity\SessionType::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\SessionType::setCalendarColor()
     * @covers \Ilios\CoreBundle\Entity\SessionType::getCalendarColor()
     */
    public function testSetSessionTypeCalendarColor()
    {
        $this->basicSetTest('calendarColor', 'hexcolor');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\SessionType::setActive
     * @covers \Ilios\CoreBundle\Entity\SessionType::isActive
     */
    public function testIsActive()
    {
        $this->booleanSetTest('active');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\SessionType::setAssessment
     * @covers \Ilios\CoreBundle\Entity\SessionType::isAssessment
     */
    public function testIsAssessment()
    {
        $this->booleanSetTest('assessment');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\SessionType::setAssessmentOption
     * @covers \Ilios\CoreBundle\Entity\SessionType::getAssessmentOption
     */
    public function testSetAssessmentOption()
    {
        $this->entitySetTest('assessmentOption', 'AssessmentOption');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\SessionType::addAamcMethod
     */
    public function testAddAamcMethod()
    {
        $this->entityCollectionAddTest('aamcMethod', 'AamcMethod');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\SessionType::removeAamcMethod
     */
    public function testRemoveAamcMethod()
    {
        $this->entityCollectionRemoveTest('aamcMethod', 'AamcMethod');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\SessionType::setAamcMethods
     * @covers \Ilios\CoreBundle\Entity\SessionType::getAamcMethods
     */
    public function testSetAamcMethods()
    {
        $this->entityCollectionSetTest('aamcMethod', 'AamcMethod');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\SessionType::addSession
     */
    public function testAddSession()
    {
        $this->entityCollectionAddTest('session', 'Session', false, false, 'setSessionType');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\SessionType::removeSession
     */
    public function testRemoveSession()
    {
        $this->expectException(\Exception::class);
        $this->entityCollectionRemoveTest('session', 'Session');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\SessionType::setSessions
     */
    public function testSetSessions()
    {
        $this->entityCollectionSetTest('session', 'Session', false, false, 'setSessionType');
    }

    public function testValidHexCodes()
    {
        $this->object->setTitle('test');
        $this->object->setSchool(m::mock('Ilios\CoreBundle\Entity\SchoolInterface'));
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
}
