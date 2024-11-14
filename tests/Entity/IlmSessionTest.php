<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\IlmSession;
use App\Entity\Session;
use DateTime;

/**
 * Tests for Entity IlmSession
 */
#[\PHPUnit\Framework\Attributes\Group('model')]
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Entity\IlmSession::class)]
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

    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getLearnerGroups());
        $this->assertCount(0, $this->object->getInstructors());
        $this->assertCount(0, $this->object->getInstructorGroups());
        $this->assertCount(0, $this->object->getLearners());
    }

    public function testSetHours(): void
    {
        $this->basicSetTest('hours', 'float');
    }

    public function testSetDueDate(): void
    {
        $this->basicSetTest('dueDate', 'datetime');
    }

    public function testAddLearnerGroup(): void
    {
        $this->entityCollectionAddTest('learnerGroup', 'LearnerGroup');
    }

    public function testRemoveLearnerGroup(): void
    {
        $this->entityCollectionRemoveTest('learnerGroup', 'LearnerGroup');
    }

    public function testGetLearnerGroups(): void
    {
        $this->entityCollectionSetTest('learnerGroup', 'LearnerGroup');
    }

    public function testAddInstructorGroup(): void
    {
        $this->entityCollectionAddTest('instructorGroup', 'InstructorGroup');
    }

    public function testRemoveInstructorGroup(): void
    {
        $this->entityCollectionRemoveTest('instructorGroup', 'InstructorGroup');
    }

    public function testGetInstructorGroups(): void
    {
        $this->entityCollectionSetTest('instructorGroup', 'InstructorGroup');
    }

    public function testAddInstructor(): void
    {
        $this->entityCollectionAddTest('instructor', 'User');
    }

    public function testRemoveInstructor(): void
    {
        $this->entityCollectionRemoveTest('instructor', 'User');
    }

    public function testGetInstructors(): void
    {
        $this->entityCollectionSetTest('instructor', 'User');
    }

    public function testAddLearner(): void
    {
        $this->entityCollectionAddTest('learner', 'User');
    }

    public function testRemoveLearner(): void
    {
        $this->entityCollectionRemoveTest('learner', 'User');
    }

    public function testGetLearners(): void
    {
        $this->entityCollectionSetTest('learner', 'User');
    }

    public function testSetSession(): void
    {
        $this->entitySetTest('session', 'Session');
    }

    protected function getObject(): IlmSession
    {
        return $this->object;
    }
}
