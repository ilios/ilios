<?php
namespace Tests\CoreBundle\Entity;

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
        $this->object->setSession(m::mock('Ilios\CoreBundle\Entity\SessionInterface'));

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
        $this->object->setSession(m::mock('Ilios\CoreBundle\Entity\SessionInterface'));

        $this->validate(0);
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Offering::__construct
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
     * @covers \Ilios\CoreBundle\Entity\Offering::setRoom
     * @covers \Ilios\CoreBundle\Entity\Offering::getRoom
     */
    public function testSetRoom()
    {
        $this->basicSetTest('room', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Offering::setSite
     * @covers \Ilios\CoreBundle\Entity\Offering::getSite
     */
    public function testSetSite()
    {
        $this->basicSetTest('site', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Offering::setStartDate
     * @covers \Ilios\CoreBundle\Entity\Offering::getStartDate
     */
    public function testSetStartDate()
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Offering::setEndDate
     * @covers \Ilios\CoreBundle\Entity\Offering::getEndDate
     */
    public function testSetEndDate()
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Offering::setSession
     * @covers \Ilios\CoreBundle\Entity\Offering::getSession
     */
    public function testSetSession()
    {
        $this->entitySetTest('session', 'Session');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Offering::addLearnerGroup
     */
    public function testAddLearnerGroup()
    {
        $this->entityCollectionAddTest('learnerGroup', 'LearnerGroup');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Offering::removeLearnerGroup
     */
    public function testRemoveLearnerGroup()
    {
        $this->entityCollectionRemoveTest('learnerGroup', 'LearnerGroup');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Offering::setLearnerGroups
     */
    public function testSetLearnerGroup()
    {
        $this->entityCollectionSetTest('learnerGroup', 'LearnerGroup');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Offering::addInstructorGroup
     */
    public function testAddInstructorGroup()
    {
        $this->entityCollectionAddTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Offering::removeInstructorGroup
     */
    public function testRemoveInstructorGroup()
    {
        $this->entityCollectionRemoveTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Offering::setInstructorGroups
     */
    public function testSetInstructorGroup()
    {
        $this->entityCollectionSetTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Offering::addLearner
     */
    public function testAddLearner()
    {
        $this->entityCollectionAddTest('learner', 'User');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Offering::removeLearner
     */
    public function testRemoveLearner()
    {
        $this->entityCollectionRemoveTest('learner', 'User');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Offering::setLearners
     */
    public function testSetLearner()
    {
        $this->entityCollectionSetTest('learner', 'User');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Offering::addInstructor
     */
    public function testAddInstructor()
    {
        $this->entityCollectionAddTest('instructor', 'User');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Offering::removeInstructor
     */
    public function testRemoveInstructor()
    {
        $this->entityCollectionRemoveTest('instructor', 'User');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Offering::setInstructors
     */
    public function testSetInstructor()
    {
        $this->entityCollectionSetTest('instructor', 'User');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Offering::getSchool
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
