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
     * @covers Ilios\CoreBundle\Entity\SessionType::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getAamcMethods());
        $this->assertEmpty($this->object->getSessions());
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionType::setTitle
     * @covers Ilios\CoreBundle\Entity\SessionType::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionType::setSessionTypeCssClass
     * @covers Ilios\CoreBundle\Entity\SessionType::getSessionTypeCssClass
     */
    public function testSetSessionTypeCssClass()
    {
        $this->basicSetTest('sessionTypeCssClass', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionType::setAssessment
     * @covers Ilios\CoreBundle\Entity\SessionType::isAssessment
     */
    public function testIsAssessment()
    {
        $this->booleanSetTest('assessment');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionType::setAssessmentOption
     * @covers Ilios\CoreBundle\Entity\SessionType::getAssessmentOption
     */
    public function testSetAssessmentOption()
    {
        $this->entitySetTest('assessmentOption', 'AssessmentOption');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionType::addAamcMethod
     */
    public function testAddAamcMethod()
    {
        $this->entityCollectionAddTest('aamcMethod', 'AamcMethod');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionType::removeAamcMethod
     */
    public function testRemoveAamcMethod()
    {
        $this->entityCollectionRemoveTest('aamcMethod', 'AamcMethod');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionType::setAamcMethods
     * @covers Ilios\CoreBundle\Entity\SessionType::getAamcMethods
     */
    public function testSetAamcMethods()
    {
        $this->entityCollectionSetTest('aamcMethod', 'AamcMethod');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionType::addSession
     */
    public function testAddSession()
    {
        $this->entityCollectionAddTest('session', 'Session');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionType::removeSession
     */
    public function testRemoveSession()
    {
        $this->entityCollectionRemoveTest('session', 'Session');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionType::setSessions
     */
    public function testSetSessions()
    {
        $this->entityCollectionSetTest('session', 'Session');
    }
}
