<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Cohort;
use App\Entity\Program;
use App\Entity\ProgramYear;
use App\Entity\School;

/**
 * Tests for Entity Cohort
 * @group model
 */
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

    /**
     * @covers \App\Entity\Cohort::__construct
     */
    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getCourses());
        $this->assertCount(0, $this->object->getLearnerGroups());
        $this->assertCount(0, $this->object->getUsers());
    }

    /**
     * @covers \App\Entity\Cohort::setTitle
     * @covers \App\Entity\Cohort::getTitle
     */
    public function testSetTitle(): void
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\Cohort::setProgramYear
     * @covers \App\Entity\Cohort::getProgramYear
     */
    public function testSetProgramYear(): void
    {
        $this->entitySetTest('programYear', 'ProgramYear');
    }

    /**
     * @covers \App\Entity\Cohort::addCourse
     */
    public function testAddCourse(): void
    {
        $this->entityCollectionAddTest('course', 'Course', false, false, 'addCohort');
    }

    /**
     * @covers \App\Entity\Cohort::removeCourse
     */
    public function testRemoveCourse(): void
    {
        $this->entityCollectionRemoveTest('course', 'Course', false, false, false, 'removeCohort');
    }

    /**
     * @covers \App\Entity\Cohort::getCourses
     */
    public function testGetCourses(): void
    {
        $this->entityCollectionSetTest('course', 'Course', false, false, 'addCohort');
    }

    /**
     * @covers \App\Entity\Cohort::addUser
     */
    public function testAddUser(): void
    {
        $this->entityCollectionAddTest('user', 'User', false, false, 'addCohort');
    }

    /**
     * @covers \App\Entity\Cohort::removeUser
     */
    public function testRemoveUser(): void
    {
        $this->entityCollectionRemoveTest('user', 'User', false, false, false, 'removeCohort');
    }

    /**
     * @covers \App\Entity\Cohort::getUsers
     */
    public function testGetUsers(): void
    {
        $this->entityCollectionSetTest('user', 'User', false, false, 'addCohort');
    }

    /**
     * @covers \App\Entity\Cohort::addLearnerGroup
     */
    public function testAddLearnerGroup(): void
    {
        $this->entityCollectionSetTest('learnerGroup', 'LearnerGroup');
    }

    /**
     * @covers \App\Entity\Cohort::removeLearnerGroup
     */
    public function testRemoveLearnerGroup(): void
    {
        $this->entityCollectionSetTest('learnerGroup', 'LearnerGroup');
    }

    /**
     * @covers \App\Entity\Cohort::getLearnerGroups
     */
    public function testGetLearnerGroups(): void
    {
        $this->entityCollectionSetTest('learnerGroup', 'LearnerGroup');
    }

    /**
     * @covers \App\Entity\Cohort::getProgram
     */
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

    /**
     * @covers \App\Entity\Cohort::getSchool
     */
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
