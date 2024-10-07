<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Course;
use App\Entity\Offering;
use App\Entity\School;
use App\Entity\Session;
use App\Entity\SessionInterface;
use DateTime;
use Mockery as m;

/**
 * Tests for Entity Offering
 * @group model
 */
class OfferingTest extends EntityBase
{
    protected Offering $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new Offering();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'startDate',
            'endDate',
        ];
        $this->object->setSession(m::mock(SessionInterface::class));

        $this->validateNotBlanks($notBlank);

        $this->object->setStartDate(new DateTime());
        $this->object->setEndDate(new DateTime());
        $this->object->setRoom('');
        $this->object->setSite('');
        $this->validate(0);
        $this->object->setRoom('test');
        $this->object->setSite('test');
        $this->validate(0);
    }

    public function testNotNullValidation(): void
    {
        $notNulls = [
            'session',
        ];

        $this->object->setRoom('RCF 112');
        $this->object->setStartDate(new DateTime());
        $this->object->setEndDate(new DateTime());

        $this->validateNotNulls($notNulls);
        $this->object->setSession(m::mock(SessionInterface::class));

        $this->validate(0);
    }

    /**
     * @covers \App\Entity\Offering::__construct
     */
    public function testConstructor(): void
    {
        $this->assertEmpty($this->object->getLearnerGroups());
        $this->assertEmpty($this->object->getInstructorGroups());
        $this->assertEmpty($this->object->getLearners());
        $this->assertEmpty($this->object->getInstructors());
        $this->assertNotEmpty($this->object->getUpdatedAt());
    }

    /**
     * @covers \App\Entity\Offering::setRoom
     * @covers \App\Entity\Offering::getRoom
     */
    public function testSetRoom(): void
    {
        $this->basicSetTest('room', 'string');
    }

    /**
     * @covers \App\Entity\Offering::setSite
     * @covers \App\Entity\Offering::getSite
     */
    public function testSetSite(): void
    {
        $this->basicSetTest('site', 'string');
    }

    /**
     * @covers \App\Entity\Offering::setUrl
     * @covers \App\Entity\Offering::getUrl
     */
    public function testSetUrl(): void
    {
        $this->basicSetTest('url', 'string');
    }

    public function testValidateUrl(): void
    {
        $this->object->setUrl('something');
        $errors = $this->validate(4);
        $this->assertTrue(
            array_key_exists('url', $errors),
            "url key not found in errors: " . var_export(array_keys($errors), true)
        );
        $this->assertSame('This value is not a valid URL.', $errors['url']);

        $this->object->setUrl('http://example.edu');
        $this->validate(3);

        $this->object->setUrl(null);
        $this->validate(3);
    }

    /**
     * @covers \App\Entity\Offering::setStartDate
     * @covers \App\Entity\Offering::getStartDate
     */
    public function testSetStartDate(): void
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    /**
     * @covers \App\Entity\Offering::setEndDate
     * @covers \App\Entity\Offering::getEndDate
     */
    public function testSetEndDate(): void
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    /**
     * @covers \App\Entity\Offering::setSession
     * @covers \App\Entity\Offering::getSession
     */
    public function testSetSession(): void
    {
        $this->entitySetTest('session', 'Session');
    }

    /**
     * @covers \App\Entity\Offering::addLearnerGroup
     */
    public function testAddLearnerGroup(): void
    {
        $this->entityCollectionAddTest('learnerGroup', 'LearnerGroup');
    }

    /**
     * @covers \App\Entity\Offering::removeLearnerGroup
     */
    public function testRemoveLearnerGroup(): void
    {
        $this->entityCollectionRemoveTest('learnerGroup', 'LearnerGroup');
    }

    /**
     * @covers \App\Entity\Offering::setLearnerGroups
     */
    public function testSetLearnerGroup(): void
    {
        $this->entityCollectionSetTest('learnerGroup', 'LearnerGroup');
    }

    /**
     * @covers \App\Entity\Offering::addInstructorGroup
     */
    public function testAddInstructorGroup(): void
    {
        $this->entityCollectionAddTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \App\Entity\Offering::removeInstructorGroup
     */
    public function testRemoveInstructorGroup(): void
    {
        $this->entityCollectionRemoveTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \App\Entity\Offering::setInstructorGroups
     */
    public function testSetInstructorGroup(): void
    {
        $this->entityCollectionSetTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \App\Entity\Offering::addLearner
     */
    public function testAddLearner(): void
    {
        $this->entityCollectionAddTest('learner', 'User');
    }

    /**
     * @covers \App\Entity\Offering::removeLearner
     */
    public function testRemoveLearner(): void
    {
        $this->entityCollectionRemoveTest('learner', 'User');
    }

    /**
     * @covers \App\Entity\Offering::setLearners
     */
    public function testSetLearner(): void
    {
        $this->entityCollectionSetTest('learner', 'User');
    }

    /**
     * @covers \App\Entity\Offering::addInstructor
     */
    public function testAddInstructor(): void
    {
        $this->entityCollectionAddTest('instructor', 'User');
    }

    /**
     * @covers \App\Entity\Offering::removeInstructor
     */
    public function testRemoveInstructor(): void
    {
        $this->entityCollectionRemoveTest('instructor', 'User');
    }

    /**
     * @covers \App\Entity\Offering::setInstructors
     */
    public function testSetInstructor(): void
    {
        $this->entityCollectionSetTest('instructor', 'User');
    }

    /**
     * @covers \App\Entity\Offering::getSchool
     */
    public function testGetSchool(): void
    {
        $school = new School();
        $course = new Course();
        $course->setSchool($school);
        $session = new Session();
        $session->setCourse($course);
        $offering = new Offering();
        $offering->setSession($session);
        $this->assertSame($school, $offering->getSchool());
    }

    protected function getObject(): Offering
    {
        return $this->object;
    }
}
