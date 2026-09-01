<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Entity\Course;
use App\Entity\Offering;
use App\Entity\School;
use App\Entity\Session;
use App\Entity\SessionInterface;
use DateTime;
use Mockery as m;

/**
 * Tests for Entity Offering
 */
#[Group('model')]
#[CoversClass(Offering::class)]
final class OfferingTest extends EntityBase
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
        $this->object->setEndDate(new DateTime('+1 minutes'));
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
        $this->object->setEndDate(new DateTime('+1 minute'));

        $this->validateNotNulls($notNulls);
        $this->object->setSession(m::mock(SessionInterface::class));

        $this->validate(0);
    }

    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getLearnerGroups());
        $this->assertCount(0, $this->object->getInstructorGroups());
        $this->assertCount(0, $this->object->getLearners());
        $this->assertCount(0, $this->object->getInstructors());
    }

    public function testSetRoom(): void
    {
        $this->basicSetTest('room', 'string');
    }

    public function testSetSite(): void
    {
        $this->basicSetTest('site', 'string');
    }

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

    public function testSetStartDate(): void
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    public function testSetEndDate(): void
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    public function testSetSession(): void
    {
        $this->entitySetTest('session', 'Session');
    }

    public function testAddLearnerGroup(): void
    {
        $this->entityCollectionAddTest('learnerGroup', 'LearnerGroup');
    }

    public function testRemoveLearnerGroup(): void
    {
        $this->entityCollectionRemoveTest('learnerGroup', 'LearnerGroup');
    }

    public function testSetLearnerGroup(): void
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

    public function testSetInstructorGroup(): void
    {
        $this->entityCollectionSetTest('instructorGroup', 'InstructorGroup');
    }

    public function testAddLearner(): void
    {
        $this->entityCollectionAddTest('learner', 'User');
    }

    public function testRemoveLearner(): void
    {
        $this->entityCollectionRemoveTest('learner', 'User');
    }

    public function testSetLearner(): void
    {
        $this->entityCollectionSetTest('learner', 'User');
    }

    public function testAddInstructor(): void
    {
        $this->entityCollectionAddTest('instructor', 'User');
    }

    public function testRemoveInstructor(): void
    {
        $this->entityCollectionRemoveTest('instructor', 'User');
    }

    public function testSetInstructor(): void
    {
        $this->entityCollectionSetTest('instructor', 'User');
    }

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

    public function testStartAndEndDateMustBeSixtySecondsApart(): void
    {
        $this->object->setSession(m::mock(SessionInterface::class));
        $this->object->setStartDate(new DateTime('2005-06-24 18:24:00'));

        // Same minute should fail.
        $this->object->setEndDate(new DateTime('2005-06-24 18:24:59'));
        $errors = $this->validate(1);

        $this->assertArrayHasKey('endDate', $errors);
        $this->assertSame(
            'Offering must have a duration of at least one minute.',
            $errors['endDate']
        );

        // Different minute should pass, even if less than 60 seconds apart.
        $this->object->setStartDate(new DateTime());
        $this->object->setEndDate(new DateTime('+60 seconds'));

        $this->validate(0);
    }

    protected function getObject(): Offering
    {
        return $this->object;
    }
}
