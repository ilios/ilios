<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\IlmSession;
use AppBundle\Entity\Session;
use Mockery as m;

/**
 * Tests for Entity IlmSession
 */
class IlmSessionTest extends EntityBase
{
    /**
     * @var IlmSession
     */
    protected $object;

    /**
     * Instantiate a IlmSession object
     */
    protected function setUp()
    {
        $this->object = new IlmSession;
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'session',
            'hours',
            'dueDate'
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setSession(new Session());
        $this->object->setHours(55.1);
        $this->object->setDueDate(new \DateTime());
        $this->validate(0);
    }

    /**
     * Ensure we can set a float for hours
     */
    public function testHourValidation()
    {
        $this->object->setSession(new Session());
        $this->object->setHours(55);
        $this->object->setDueDate(new \DateTime());
        $this->object->setHours(1.33);
        $this->validate(0);
    }

    /**
     * @covers \AppBundle\Entity\IlmSession::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getLearnerGroups());
        $this->assertEmpty($this->object->getInstructors());
        $this->assertEmpty($this->object->getInstructorGroups());
        $this->assertEmpty($this->object->getLearners());
    }

    /**
     * @covers \AppBundle\Entity\IlmSession::setHours
     * @covers \AppBundle\Entity\IlmSession::getHours
     */
    public function testSetHours()
    {
        $this->basicSetTest('hours', 'float');
    }

    /**
     * @covers \AppBundle\Entity\IlmSession::setDueDate
     * @covers \AppBundle\Entity\IlmSession::getDueDate
     */
    public function testSetDueDate()
    {
        $this->basicSetTest('dueDate', 'datetime');
    }

    /**
     * @covers \AppBundle\Entity\IlmSession::addLearnerGroup
     */
    public function testAddLearnerGroup()
    {
        $this->entityCollectionAddTest('learnerGroup', 'LearnerGroup');
    }

    /**
     * @covers \AppBundle\Entity\IlmSession::removeLearnerGroup
     */
    public function testRemoveLearnerGroup()
    {
        $this->entityCollectionRemoveTest('learnerGroup', 'LearnerGroup');
    }

    /**
     * @covers \AppBundle\Entity\IlmSession::getLearnerGroups
     */
    public function testGetLearnerGroups()
    {
        $this->entityCollectionSetTest('learnerGroup', 'LearnerGroup');
    }

    /**
     * @covers \AppBundle\Entity\IlmSession::addInstructorGroup
     */
    public function testAddInstructorGroup()
    {
        $this->entityCollectionAddTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \AppBundle\Entity\IlmSession::removeInstructorGroup
     */
    public function testRemoveInstructorGroup()
    {
        $this->entityCollectionRemoveTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \AppBundle\Entity\IlmSession::getInstructorGroups
     */
    public function testGetInstructorGroups()
    {
        $this->entityCollectionSetTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \AppBundle\Entity\IlmSession::addInstructor
     */
    public function testAddInstructor()
    {
        $this->entityCollectionAddTest('instructor', 'User');
    }

    /**
     * @covers \AppBundle\Entity\IlmSession::removeInstructor
     */
    public function testRemoveInstructor()
    {
        $this->entityCollectionRemoveTest('instructor', 'User');
    }

    /**
     * @covers \AppBundle\Entity\IlmSession::getInstructors
     */
    public function testGetInstructors()
    {
        $this->entityCollectionSetTest('instructor', 'User');
    }

    /**
     * @covers \AppBundle\Entity\IlmSession::addLearner
     */
    public function testAddLearner()
    {
        $this->entityCollectionAddTest('learner', 'User');
    }

    /**
     * @covers \AppBundle\Entity\IlmSession::removeLearner
     */
    public function testRemoveLearner()
    {
        $this->entityCollectionRemoveTest('learner', 'User');
    }

    /**
     * @covers \AppBundle\Entity\IlmSession::getLearners
     */
    public function testGetLearners()
    {
        $this->entityCollectionSetTest('learner', 'User');
    }

    /**
     * @covers \AppBundle\Entity\IlmSession::setSession
     * @covers \AppBundle\Entity\IlmSession::getSession
     */
    public function testSetSession()
    {
        $this->entitySetTest('session', 'Session');
    }
}
