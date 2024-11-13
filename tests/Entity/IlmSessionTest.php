<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\IlmSession;
use App\Entity\Session;
use DateTime;

/**
 * Tests for Entity IlmSession
 * @group model
 */
class IlmSessionTest extends EntityBase
{
    protected IlmSession $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new IlmSession();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'session',
            'hours',
            'dueDate',
        ];
        $this->validateNotBlanks($notBlank);

        $this->object->setSession(new Session());
        $this->object->setHours(55.1);
        $this->object->setDueDate(new DateTime());
        $this->validate(0);
    }

    /**
     * Ensure we can set a float for hours
     */
    public function testHourValidation(): void
    {
        $this->object->setSession(new Session());
        $this->object->setHours(55);
        $this->object->setDueDate(new DateTime());
        $this->object->setHours(1.33);
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\IlmSession::__construct
     */
    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getLearnerGroups());
        $this->assertCount(0, $this->object->getInstructors());
        $this->assertCount(0, $this->object->getInstructorGroups());
        $this->assertCount(0, $this->object->getLearners());
    }

    /**
     * @covers \App\Entity\IlmSession::setHours
     * @covers \App\Entity\IlmSession::getHours
     */
    public function testSetHours(): void
    {
        $this->basicSetTest('hours', 'float');
    }

    /**
     * @covers \App\Entity\IlmSession::setDueDate
     * @covers \App\Entity\IlmSession::getDueDate
     */
    public function testSetDueDate(): void
    {
        $this->basicSetTest('dueDate', 'datetime');
    }

    /**
     * @covers \App\Entity\IlmSession::addLearnerGroup
     */
    public function testAddLearnerGroup(): void
    {
        $this->entityCollectionAddTest('learnerGroup', 'LearnerGroup');
    }

    /**
     * @covers \App\Entity\IlmSession::removeLearnerGroup
     */
    public function testRemoveLearnerGroup(): void
    {
        $this->entityCollectionRemoveTest('learnerGroup', 'LearnerGroup');
    }

    /**
     * @covers \App\Entity\IlmSession::getLearnerGroups
     */
    public function testGetLearnerGroups(): void
    {
        $this->entityCollectionSetTest('learnerGroup', 'LearnerGroup');
    }

    /**
     * @covers \App\Entity\IlmSession::addInstructorGroup
     */
    public function testAddInstructorGroup(): void
    {
        $this->entityCollectionAddTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \App\Entity\IlmSession::removeInstructorGroup
     */
    public function testRemoveInstructorGroup(): void
    {
        $this->entityCollectionRemoveTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \App\Entity\IlmSession::getInstructorGroups
     */
    public function testGetInstructorGroups(): void
    {
        $this->entityCollectionSetTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \App\Entity\IlmSession::addInstructor
     */
    public function testAddInstructor(): void
    {
        $this->entityCollectionAddTest('instructor', 'User');
    }

    /**
     * @covers \App\Entity\IlmSession::removeInstructor
     */
    public function testRemoveInstructor(): void
    {
        $this->entityCollectionRemoveTest('instructor', 'User');
    }

    /**
     * @covers \App\Entity\IlmSession::getInstructors
     */
    public function testGetInstructors(): void
    {
        $this->entityCollectionSetTest('instructor', 'User');
    }

    /**
     * @covers \App\Entity\IlmSession::addLearner
     */
    public function testAddLearner(): void
    {
        $this->entityCollectionAddTest('learner', 'User');
    }

    /**
     * @covers \App\Entity\IlmSession::removeLearner
     */
    public function testRemoveLearner(): void
    {
        $this->entityCollectionRemoveTest('learner', 'User');
    }

    /**
     * @covers \App\Entity\IlmSession::getLearners
     */
    public function testGetLearners(): void
    {
        $this->entityCollectionSetTest('learner', 'User');
    }

    /**
     * @covers \App\Entity\IlmSession::setSession
     * @covers \App\Entity\IlmSession::getSession
     */
    public function testSetSession(): void
    {
        $this->entitySetTest('session', 'Session');
    }

    protected function getObject(): IlmSession
    {
        return $this->object;
    }
}
