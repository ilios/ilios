<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\Course;
use Ilios\CoreBundle\Entity\Offering;
use Ilios\CoreBundle\Entity\School;
use Ilios\CoreBundle\Entity\Session;
use Mockery as m;

/**
 * Tests for Entity Offering
 */
class OfferingTest extends EntityBase
{
    /**
     * @var Offering
     */
    protected $object;

    /**
     * Instantiate a Offering object
     */
    protected function setUp()
    {
        $this->object = new Offering;
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'room',
            'startDate',
            'endDate'
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setRoom('RCF 112');
        $this->object->setStartDate(new \DateTime());
        $this->object->setEndDate(new \DateTime());
        $this->validate(0);
    }
    /**
     * @covers Ilios\CoreBundle\Entity\Offering::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getLearnerGroups());
        $this->assertEmpty($this->object->getInstructorGroups());
        $this->assertEmpty($this->object->getLearners());
        $this->assertEmpty($this->object->getInstructors());
        $this->assertNotEmpty($this->object->getUpdatedAt());
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::setRoom
     * @covers Ilios\CoreBundle\Entity\Offering::getRoom
     */
    public function testSetRoom()
    {
        $this->basicSetTest('room', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::setStartDate
     * @covers Ilios\CoreBundle\Entity\Offering::getStartDate
     */
    public function testSetStartDate()
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::setEndDate
     * @covers Ilios\CoreBundle\Entity\Offering::getEndDate
     */
    public function testSetEndDate()
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::setDeleted
     * @covers Ilios\CoreBundle\Entity\Offering::isDeleted
     */
    public function testSetDeleted()
    {
        $this->booleanSetTest('deleted');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::setSession
     * @covers Ilios\CoreBundle\Entity\Offering::getSession
     */
    public function testSetSession()
    {
        $this->softDeleteEntitySetTest('session', 'Session');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::addLearnerGroup
     */
    public function testAddLearnerGroup()
    {
        $this->entityCollectionAddTest('learnerGroup', 'LearnerGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::setPublishEvent
     * @covers Ilios\CoreBundle\Entity\Offering::getPublishEvent
     */
    public function testSetPublishEvent()
    {
        $this->entitySetTest('publishEvent', 'PublishEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Offering::getSchool
     */
    public function testGetSchool()
    {
        $school = new School();
        $course = new Course();
        $course->setSchool($school);
        $session = new Session();
        $session->setCourse($course);
        $this->object->setSession($session);

        $this->assertSame($school, $this->object->getSchool());

        $school->setDeleted(true);
        $this->assertNull($this->object->getSchool());

        $school->setDeleted(false);
        $course->setDeleted(true);
        $this->assertNull($this->object->getSchool());

        $course->setDeleted(false);
        $session->setDeleted(true);
        $this->assertNull($this->object->getSchool());
    }
}
