<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\CohortInterface;
use App\Entity\Cohort;
use App\Entity\LearnerGroup;
use App\Entity\Program;
use App\Entity\ProgramYear;
use App\Entity\School;
use Mockery as m;

/**
 * Tests for Entity LearnerGroup
 * @group model
 */
class LearnerGroupTest extends EntityBase
{
    protected LearnerGroup $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new LearnerGroup();
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
        $this->object->setCohort(m::mock(CohortInterface::class));

        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('test');
        $this->object->setLocation('');
        $this->validate(0);
        $this->object->setLocation('test');
        $this->validate(0);
    }

    public function testNotNullValidation(): void
    {
        $notNulls = [
            'cohort',
        ];
        $this->object->setTitle('test');
        $this->validateNotNulls($notNulls);

        $this->object->setCohort(m::mock(CohortInterface::class));

        $this->validate(0);
    }

    /**
     * @covers \App\Entity\LearnerGroup::__construct
     */
    public function testConstructor(): void
    {
        $this->assertEmpty($this->object->getIlmSessions());
        $this->assertEmpty($this->object->getInstructorGroups());
        $this->assertEmpty($this->object->getInstructors());
        $this->assertEmpty($this->object->getOfferings());
        $this->assertEmpty($this->object->getUsers());
        $this->assertEmpty($this->object->getChildren());
        $this->assertEmpty($this->object->getDescendants());
    }

    /**
     * @covers \App\Entity\LearnerGroup::setTitle
     * @covers \App\Entity\LearnerGroup::getTitle
     */
    public function testSetTitle(): void
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\LearnerGroup::setLocation
     * @covers \App\Entity\LearnerGroup::getLocation
     */
    public function testSetLocation(): void
    {
        $this->basicSetTest('location', 'string');
    }

    /**
     * @covers \App\Entity\LearnerGroup::setUrl
     * @covers \App\Entity\LearnerGroup::getUrl
     */
    public function testSetUrl(): void
    {
        $this->basicSetTest('url', 'string');
    }

    /**
     * @covers \App\Entity\LearnerGroup::setNeedsAccommodation
     * @covers \App\Entity\LearnerGroup::getNeedsAccommodation
     */
    public function testSetNeedsAccommodation(): void
    {
        $this->basicSetTest('needsAccommodation', 'bool');
    }

    /**
     * @covers \App\Entity\LearnerGroup::setCohort
     * @covers \App\Entity\LearnerGroup::getCohort
     */
    public function testSetCohort(): void
    {
        $this->entitySetTest('cohort', 'Cohort');
    }

    /**
     * @covers \App\Entity\LearnerGroup::addIlmSession
     */
    public function testAddIlmSession(): void
    {
        $this->entityCollectionAddTest('ilmSession', 'IlmSession', false, false, 'addLearnerGroup');
    }

    /**
     * @covers \App\Entity\LearnerGroup::removeIlmSession
     */
    public function testRemoveIlmSession(): void
    {
        $this->entityCollectionRemoveTest('ilmSession', 'IlmSession', false, false, false, 'removeLearnerGroup');
    }

    /**
     * @covers \App\Entity\LearnerGroup::getIlmSessions
     */
    public function testGetIlmSessions(): void
    {
        $this->entityCollectionSetTest('ilmSession', 'IlmSession', false, false, 'addLearnerGroup');
    }

    /**
     * @covers \App\Entity\LearnerGroup::addOffering
     */
    public function testAddOffering(): void
    {
        $this->entityCollectionAddTest('offering', 'Offering', false, false, 'addLearnerGroup');
    }

    /**
     * @covers \App\Entity\LearnerGroup::removeOffering
     */
    public function testRemoveOffering(): void
    {
        $this->entityCollectionRemoveTest('offering', 'Offering', false, false, false, 'removeLearnerGroup');
    }

    /**
     * @covers \App\Entity\LearnerGroup::getOfferings
     */
    public function testGetOfferings(): void
    {
        $this->entityCollectionSetTest('offering', 'Offering', false, false, 'addLearnerGroup');
    }

    /**
     * @covers \App\Entity\LearnerGroup::addInstructorGroup
     */
    public function testAddInstructorGroup(): void
    {
        $this->entityCollectionAddTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \App\Entity\LearnerGroup::removeInstructorGroup
     */
    public function testRemoveInstructorGroup(): void
    {
        $this->entityCollectionRemoveTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \App\Entity\LearnerGroup::getInstructorGroups
     */
    public function testGetInstructorGroups(): void
    {
        $this->entityCollectionSetTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \App\Entity\LearnerGroup::addUser
     */
    public function testAddUser(): void
    {
        $this->entityCollectionAddTest('user', 'User');
    }

    /**
     * @covers \App\Entity\LearnerGroup::removeUser
     */
    public function testRemoveUser(): void
    {
        $this->entityCollectionRemoveTest('user', 'User');
    }

    /**
     * @covers \App\Entity\LearnerGroup::getUsers
     */
    public function testGetUsers(): void
    {
        $this->entityCollectionSetTest('user', 'User');
    }

    /**
     * @covers \App\Entity\LearnerGroup::addInstructor
     */
    public function testAddInstructor(): void
    {
        $this->entityCollectionAddTest('instructor', 'User');
    }

    /**
     * @covers \App\Entity\LearnerGroup::removeInstructor
     */
    public function testRemoveInstructor(): void
    {
        $this->entityCollectionRemoveTest('instructor', 'User');
    }

    /**
     * @covers \App\Entity\LearnerGroup::getInstructors
     */
    public function testGetInstructors(): void
    {
        $this->entityCollectionSetTest('instructor', 'User');
    }

    /**
     * @covers \App\Entity\LearnerGroup::getProgramYear
     */
    public function testGetProgramYear(): void
    {
        $programYear = new ProgramYear();
        $cohort = new Cohort();
        $cohort->setProgramYear($programYear);
        $learnerGroup = new LearnerGroup();
        $learnerGroup->setCohort($cohort);
        $this->assertEquals($programYear, $learnerGroup->getProgramYear());

        $cohort = new Cohort();
        $learnerGroup = new LearnerGroup();
        $learnerGroup->setCohort($cohort);
        $this->assertNull($learnerGroup->getProgramYear());
    }

    /**
     * @covers \App\Entity\LearnerGroup::getProgram
     */
    public function testGetProgram(): void
    {
        $program = new Program();
        $programYear = new ProgramYear();
        $programYear->setProgram($program);
        $cohort = new Cohort();
        $cohort->setProgramYear($programYear);
        $learnerGroup = new LearnerGroup();
        $learnerGroup->setCohort($cohort);
        $this->assertEquals($program, $learnerGroup->getProgram());

        $cohort = new Cohort();
        $learnerGroup = new LearnerGroup();
        $learnerGroup->setCohort($cohort);
        $this->assertNull($learnerGroup->getProgram());
    }

    /**
     * @covers \App\Entity\LearnerGroup::getSchool
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
        $learnerGroup = new LearnerGroup();
        $learnerGroup->setCohort($cohort);
        $this->assertEquals($school, $learnerGroup->getSchool());

        $cohort = new Cohort();
        $learnerGroup = new LearnerGroup();
        $learnerGroup->setCohort($cohort);
        $this->assertNull($learnerGroup->getSchool());
    }

    /**
     * @covers \App\Entity\LearnerGroup::addChild
     */
    public function testAddChild(): void
    {
        $this->entityCollectionAddTest('child', 'LearnerGroup', 'getChildren');
    }

    /**
     * @covers \App\Entity\LearnerGroup::removeChild
     */
    public function testRemoveChild(): void
    {
        $this->entityCollectionRemoveTest('child', 'LearnerGroup', 'getChildren');
    }

    /**
     * @covers \App\Entity\LearnerGroup::getChildren
     * @covers \App\Entity\LearnerGroup::setChildren
     */
    public function testGetChildren(): void
    {
        $this->entityCollectionSetTest('child', 'LearnerGroup', 'getChildren', 'setChildren');
    }

    /**
     * @covers \App\Entity\LearnerGroup::setAncestor
     * @covers \App\Entity\LearnerGroup::getAncestor
     */
    public function testSetAncestor(): void
    {
        $this->entitySetTest('ancestor', 'LearnerGroup');
    }

    /**
     * @covers \App\Entity\LearnerGroup::getAncestorOrSelf
     */
    public function testGetAncestorOrSelfWithAncestor(): void
    {
        $ancestor = m::mock(LearnerGroup::class);
        $this->object->setAncestor($ancestor);
        $this->assertSame($ancestor, $this->object->getAncestorOrSelf());
    }

    /**
     * @covers \App\Entity\LearnerGroup::getAncestorOrSelf
     */
    public function testGetAncestorOrSelfWithNoAncestor(): void
    {
        $this->assertSame($this->object, $this->object->getAncestorOrSelf());
    }

    /**
     * @covers \App\Entity\LearnerGroup::addDescendant
     */
    public function testAddDescendant(): void
    {
        $this->entityCollectionAddTest('descendant', 'LearnerGroup', 'getDescendants', 'addDescendant', 'setAncestor');
    }

    /**
     * @covers \App\Entity\LearnerGroup::removeDescendant
     */
    public function testRemoveDescendant(): void
    {
        $this->entityCollectionRemoveTest('descendant', 'LearnerGroup');
    }

    /**
     * @covers \App\Entity\LearnerGroup::getDescendants
     * @covers \App\Entity\LearnerGroup::setDescendants
     */
    public function testGetDescendants(): void
    {
        $this->entityCollectionSetTest('descendant', 'LearnerGroup', 'getDescendants', 'setDescendants', 'setAncestor');
    }

    protected function getObject(): LearnerGroup
    {
        return $this->object;
    }
}
