<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Cohort;
use App\Entity\Program;
use App\Entity\ProgramYear;
use App\Entity\School;

/**
 * Tests for Entity Cohort
 */
#[\PHPUnit\Framework\Attributes\Group('model')]
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Entity\Cohort::class)]
class CohortTest extends EntityBase
{
    protected Cohort $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new Cohort();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'title',
        ];
        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('up to sixty char');
        $this->validate(0);
    }

    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getCourses());
        $this->assertCount(0, $this->object->getLearnerGroups());
        $this->assertCount(0, $this->object->getUsers());
    }

    public function testSetTitle(): void
    {
        $this->basicSetTest('title', 'string');
    }

    public function testSetProgramYear(): void
    {
        $this->entitySetTest('programYear', 'ProgramYear');
    }

    public function testAddCourse(): void
    {
        $this->entityCollectionAddTest('course', 'Course', false, false, 'addCohort');
    }

    public function testRemoveCourse(): void
    {
        $this->entityCollectionRemoveTest('course', 'Course', false, false, false, 'removeCohort');
    }

    public function testGetCourses(): void
    {
        $this->entityCollectionSetTest('course', 'Course', false, false, 'addCohort');
    }

    public function testAddUser(): void
    {
        $this->entityCollectionAddTest('user', 'User', false, false, 'addCohort');
    }

    public function testRemoveUser(): void
    {
        $this->entityCollectionRemoveTest('user', 'User', false, false, false, 'removeCohort');
    }

    public function testGetUsers(): void
    {
        $this->entityCollectionSetTest('user', 'User', false, false, 'addCohort');
    }

    public function testAddLearnerGroup(): void
    {
        $this->entityCollectionSetTest('learnerGroup', 'LearnerGroup');
    }

    public function testRemoveLearnerGroup(): void
    {
        $this->entityCollectionSetTest('learnerGroup', 'LearnerGroup');
    }

    public function testGetLearnerGroups(): void
    {
        $this->entityCollectionSetTest('learnerGroup', 'LearnerGroup');
    }

    public function testGetProgram(): void
    {
        $program = new Program();
        $programYear = new ProgramYear();
        $programYear->setProgram($program);
        $cohort = new Cohort();
        $cohort->setProgramYear($programYear);
        $this->assertEquals($program, $cohort->getProgram());

        $cohort = new Cohort();
        $this->assertNull($cohort->getProgram());
    }

    public function testGetSchool(): void
    {
        $school = new School();
        $program = new Program();
        $program->setSchool($school);
        $programYear = new ProgramYear();
        $programYear->setProgram($program);
        $cohort = new Cohort();
        $cohort->setProgramYear($programYear);
        $this->assertEquals($school, $cohort->getSchool());

        $cohort = new Cohort();
        $this->assertNull($cohort->getSchool());
    }

    protected function getObject(): Cohort
    {
        return $this->object;
    }
}
