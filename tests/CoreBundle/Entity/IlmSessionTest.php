<?php
namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\IlmSession;
use Ilios\CoreBundle\Entity\Session;
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
     * @covers \Ilios\CoreBundle\Entity\IlmSession::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getLearnerGroups());
        $this->assertEmpty($this->object->getInstructors());
        $this->assertEmpty($this->object->getInstructorGroups());
        $this->assertEmpty($this->object->getLearners());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\IlmSession::setHours
     * @covers \Ilios\CoreBundle\Entity\IlmSession::getHours
     */
    public function testSetHours()
    {
        $this->basicSetTest('hours', 'float');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\IlmSession::setDueDate
     * @covers \Ilios\CoreBundle\Entity\IlmSession::getDueDate
     */
    public function testSetDueDate()
    {
        $this->basicSetTest('dueDate', 'datetime');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\IlmSession::addLearnerGroup
     */
    public function testAddLearnerGroup()
    {
        $this->entityCollectionAddTest('learnerGroup', 'LearnerGroup');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\IlmSession::removeLearnerGroup
     */
    public function testRemoveLearnerGroup()
    {
        $this->entityCollectionRemoveTest('learnerGroup', 'LearnerGroup');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\IlmSession::getLearnerGroups
     */
    public function testGetLearnerGroups()
    {
        $this->entityCollectionSetTest('learnerGroup', 'LearnerGroup');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\IlmSession::addInstructorGroup
     */
    public function testAddInstructorGroup()
    {
        $this->entityCollectionAddTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\IlmSession::removeInstructorGroup
     */
    public function testRemoveInstructorGroup()
    {
        $this->entityCollectionRemoveTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\IlmSession::getInstructorGroups
     */
    public function testGetInstructorGroups()
    {
        $this->entityCollectionSetTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\IlmSession::addInstructor
     */
    public function testAddInstructor()
    {
        $this->entityCollectionAddTest('instructor', 'User');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\IlmSession::removeInstructor
     */
    public function testRemoveInstructor()
    {
        $this->entityCollectionRemoveTest('instructor', 'User');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\IlmSession::getInstructors
     */
    public function testGetInstructors()
    {
        $this->entityCollectionSetTest('instructor', 'User');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\IlmSession::addLearner
     */
    public function testAddLearner()
    {
        $this->entityCollectionAddTest('learner', 'User');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\IlmSession::removeLearner
     */
    public function testRemoveLearner()
    {
        $this->entityCollectionRemoveTest('learner', 'User');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\IlmSession::getLearners
     */
    public function testGetLearners()
    {
        $this->entityCollectionSetTest('learner', 'User');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\IlmSession::setSession
     * @covers \Ilios\CoreBundle\Entity\IlmSession::getSession
     */
    public function testSetSession()
    {
        $this->entitySetTest('session', 'Session');
    }
}
