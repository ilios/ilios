<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Course;
use AppBundle\Entity\Offering;
use AppBundle\Entity\School;
use AppBundle\Entity\Session;
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
        $this->object->setSession(m::mock('AppBundle\Entity\SessionInterface'));

        $this->validateNotBlanks($notBlank);

        $this->object->setRoom('RCF 112');
        $this->object->setStartDate(new \DateTime());
        $this->object->setEndDate(new \DateTime());
        $this->validate(0);
    }

    public function testNotNullValidation()
    {
        $notNulls = array(
            'session'
        );

        $this->object->setRoom('RCF 112');
        $this->object->setStartDate(new \DateTime());
        $this->object->setEndDate(new \DateTime());

        $this->validateNotNulls($notNulls);
        $this->object->setSession(m::mock('AppBundle\Entity\SessionInterface'));

        $this->validate(0);
    }

    /**
     * @covers \AppBundle\Entity\Offering::__construct
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
     * @covers \AppBundle\Entity\Offering::setRoom
     * @covers \AppBundle\Entity\Offering::getRoom
     */
    public function testSetRoom()
    {
        $this->basicSetTest('room', 'string');
    }

    /**
     * @covers \AppBundle\Entity\Offering::setSite
     * @covers \AppBundle\Entity\Offering::getSite
     */
    public function testSetSite()
    {
        $this->basicSetTest('site', 'string');
    }

    /**
     * @covers \AppBundle\Entity\Offering::setStartDate
     * @covers \AppBundle\Entity\Offering::getStartDate
     */
    public function testSetStartDate()
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    /**
     * @covers \AppBundle\Entity\Offering::setEndDate
     * @covers \AppBundle\Entity\Offering::getEndDate
     */
    public function testSetEndDate()
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    /**
     * @covers \AppBundle\Entity\Offering::setSession
     * @covers \AppBundle\Entity\Offering::getSession
     */
    public function testSetSession()
    {
        $this->entitySetTest('session', 'Session');
    }

    /**
     * @covers \AppBundle\Entity\Offering::addLearnerGroup
     */
    public function testAddLearnerGroup()
    {
        $this->entityCollectionAddTest('learnerGroup', 'LearnerGroup');
    }

    /**
     * @covers \AppBundle\Entity\Offering::removeLearnerGroup
     */
    public function testRemoveLearnerGroup()
    {
        $this->entityCollectionRemoveTest('learnerGroup', 'LearnerGroup');
    }

    /**
     * @covers \AppBundle\Entity\Offering::setLearnerGroups
     */
    public function testSetLearnerGroup()
    {
        $this->entityCollectionSetTest('learnerGroup', 'LearnerGroup');
    }

    /**
     * @covers \AppBundle\Entity\Offering::addInstructorGroup
     */
    public function testAddInstructorGroup()
    {
        $this->entityCollectionAddTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \AppBundle\Entity\Offering::removeInstructorGroup
     */
    public function testRemoveInstructorGroup()
    {
        $this->entityCollectionRemoveTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \AppBundle\Entity\Offering::setInstructorGroups
     */
    public function testSetInstructorGroup()
    {
        $this->entityCollectionSetTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \AppBundle\Entity\Offering::addLearner
     */
    public function testAddLearner()
    {
        $this->entityCollectionAddTest('learner', 'User');
    }

    /**
     * @covers \AppBundle\Entity\Offering::removeLearner
     */
    public function testRemoveLearner()
    {
        $this->entityCollectionRemoveTest('learner', 'User');
    }

    /**
     * @covers \AppBundle\Entity\Offering::setLearners
     */
    public function testSetLearner()
    {
        $this->entityCollectionSetTest('learner', 'User');
    }

    /**
     * @covers \AppBundle\Entity\Offering::addInstructor
     */
    public function testAddInstructor()
    {
        $this->entityCollectionAddTest('instructor', 'User');
    }

    /**
     * @covers \AppBundle\Entity\Offering::removeInstructor
     */
    public function testRemoveInstructor()
    {
        $this->entityCollectionRemoveTest('instructor', 'User');
    }

    /**
     * @covers \AppBundle\Entity\Offering::setInstructors
     */
    public function testSetInstructor()
    {
        $this->entityCollectionSetTest('instructor', 'User');
    }

    /**
     * @covers \AppBundle\Entity\Offering::getSchool
     */
    public function testGetSchool()
    {
        $school = new School();
        $course = new Course();
        $course->setSchool($school);
        $session = new Session();
        $session->setCourse($course);
        $offering = new Offering();
        $offering->setSession($session);
        $this->assertSame($school, $offering->getSchool());

        $course = new Course();
        $session = new Session();
        $session->setCourse($course);
        $offering = new Offering();
        $offering->setSession($session);
        $this->assertNull($offering->getSchool());

        $session = new Session();
        $offering = new Offering();
        $offering->setSession($session);
        $this->assertNull($offering->getSchool());

        $offering = new Offering();
        $this->assertNull($offering->getSchool());
    }
}
