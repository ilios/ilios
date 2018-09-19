<?php
namespace Tests\App\Entity;

use App\Entity\Cohort;
use App\Entity\LearnerGroup;
use App\Entity\Program;
use App\Entity\ProgramYear;
use App\Entity\School;
use Mockery as m;

/**
 * Tests for Entity LearnerGroup
 */
class LearnerGroupTest extends EntityBase
{
    /**
     * @var LearnerGroup
     */
    protected $object;

    /**
     * Instantiate a LearnerGroup object
     */
    protected function setUp()
    {
        $this->object = new LearnerGroup;
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'title'
        );
        $this->object->setCohort(m::mock('App\Entity\CohortInterface'));

        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('test');
        $this->validate(0);
    }

    public function testNotNullValidation()
    {
        $notNulls = array(
            'cohort'
        );
        $this->object->setTitle('test');
        $this->validateNotNulls($notNulls);

        $this->object->setCohort(m::mock('App\Entity\CohortInterface'));

        $this->validate(0);
    }

    /**
     * @covers \App\Entity\LearnerGroup::__construct
     */
    public function testConstructor()
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
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\LearnerGroup::setLocation
     * @covers \App\Entity\LearnerGroup::getLocation
     */
    public function testSetLocation()
    {
        $this->basicSetTest('location', 'string');
    }

    /**
     * @covers \App\Entity\LearnerGroup::setCohort
     * @covers \App\Entity\LearnerGroup::getCohort
     */
    public function testSetCohort()
    {
        $this->entitySetTest('cohort', 'Cohort');
    }

    /**
     * @covers \App\Entity\LearnerGroup::addIlmSession
     */
    public function testAddIlmSession()
    {
        $this->entityCollectionAddTest('ilmSession', 'IlmSession', false, false, 'addLearnerGroup');
    }

    /**
     * @covers \App\Entity\LearnerGroup::removeIlmSession
     */
    public function testRemoveIlmSession()
    {
        $this->entityCollectionRemoveTest('ilmSession', 'IlmSession', false, false, false, 'removeLearnerGroup');
    }

    /**
     * @covers \App\Entity\LearnerGroup::getIlmSessions
     */
    public function getGetIlmSessions()
    {
        $this->entityCollectionSetTest('ilmSession', 'IlmSession', false, false, 'addLearnerGroup');
    }

    /**
     * @covers \App\Entity\LearnerGroup::addOffering
     */
    public function testAddOffering()
    {
        $this->entityCollectionAddTest('offering', 'Offering', false, false, 'addLearnerGroup');
    }

    /**
     * @covers \App\Entity\LearnerGroup::removeOffering
     */
    public function testRemoveOffering()
    {
        $this->entityCollectionRemoveTest('offering', 'Offering', false, false, false, 'removeLearnerGroup');
    }

    /**
     * @covers \App\Entity\LearnerGroup::getOfferings
     */
    public function getGetOfferings()
    {
        $this->entityCollectionSetTest('offering', 'Offering', false, false, 'addLearnerGroup');
    }

    /**
     * @covers \App\Entity\LearnerGroup::addInstructorGroup
     */
    public function testAddInstructorGroup()
    {
        $this->entityCollectionAddTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \App\Entity\LearnerGroup::removeInstructorGroup
     */
    public function testRemoveInstructorGroup()
    {
        $this->entityCollectionRemoveTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \App\Entity\LearnerGroup::getInstructorGroups
     */
    public function getGetInstructorGroups()
    {
        $this->entityCollectionSetTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \App\Entity\LearnerGroup::addUser
     */
    public function testAddUser()
    {
        $this->entityCollectionAddTest('user', 'User');
    }

    /**
     * @covers \App\Entity\LearnerGroup::removeUser
     */
    public function testRemoveUser()
    {
        $this->entityCollectionRemoveTest('user', 'User');
    }

    /**
     * @covers \App\Entity\LearnerGroup::getUsers
     */
    public function getGetUsers()
    {
        $this->entityCollectionSetTest('user', 'User');
    }

    /**
     * @covers \App\Entity\LearnerGroup::addInstructor
     */
    public function testAddInstructor()
    {
        $this->entityCollectionAddTest('instructor', 'User');
    }

    /**
     * @covers \App\Entity\LearnerGroup::removeInstructor
     */
    public function testRemoveInstructor()
    {
        $this->entityCollectionRemoveTest('instructor', 'User');
    }

    /**
     * @covers \App\Entity\LearnerGroup::getInstructors
     */
    public function getGetInstructors()
    {
        $this->entityCollectionSetTest('instructor', 'User');
    }

    /**
     * @covers \App\Entity\LearnerGroup::getProgramYear
     */
    public function testGetProgramYear()
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

        $learnerGroup = new LearnerGroup();
        $this->assertNull($learnerGroup->getProgramYear());
    }

    /**
     * @covers \App\Entity\LearnerGroup::getProgram
     */
    public function testGetProgram()
    {
        $program = new Program();
        $programYear = new ProgramYear();
        $programYear->setProgram($program);
        $cohort = new Cohort();
        $cohort->setProgramYear($programYear);
        $learnerGroup = new LearnerGroup();
        $learnerGroup->setCohort($cohort);
        $this->assertEquals($program, $learnerGroup->getProgram());

        $programYear = new ProgramYear();
        $cohort = new Cohort();
        $cohort->setProgramYear($programYear);
        $learnerGroup = new LearnerGroup();
        $learnerGroup->setCohort($cohort);
        $this->assertNull($learnerGroup->getProgram());

        $cohort = new Cohort();
        $learnerGroup = new LearnerGroup();
        $learnerGroup->setCohort($cohort);
        $this->assertNull($learnerGroup->getProgram());

        $learnerGroup = new LearnerGroup();
        $this->assertNull($learnerGroup->getProgram());
    }

    /**
     * @covers \App\Entity\LearnerGroup::getSchool
     */
    public function testGetSchool()
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

        $program = new Program();
        $programYear = new ProgramYear();
        $programYear->setProgram($program);
        $cohort = new Cohort();
        $cohort->setProgramYear($programYear);
        $learnerGroup = new LearnerGroup();
        $learnerGroup->setCohort($cohort);
        $this->assertNull($learnerGroup->getSchool());

        $programYear = new ProgramYear();
        $cohort = new Cohort();
        $cohort->setProgramYear($programYear);
        $learnerGroup = new LearnerGroup();
        $learnerGroup->setCohort($cohort);
        $this->assertNull($learnerGroup->getSchool());

        $cohort = new Cohort();
        $learnerGroup = new LearnerGroup();
        $learnerGroup->setCohort($cohort);
        $this->assertNull($learnerGroup->getSchool());

        $learnerGroup = new LearnerGroup();
        $this->assertNull($learnerGroup->getSchool());
    }

    /**
     * @covers \App\Entity\LearnerGroup::addChild
     */
    public function testAddChild()
    {
        $this->entityCollectionAddTest('child', 'LearnerGroup', 'getChildren');
    }

    /**
     * @covers \App\Entity\LearnerGroup::removeChild
     */
    public function testRemoveChild()
    {
        $this->entityCollectionRemoveTest('child', 'LearnerGroup', 'getChildren');
    }

    /**
     * @covers \App\Entity\LearnerGroup::getChildren
     * @covers \App\Entity\LearnerGroup::setChildren
     */
    public function testGetChildren()
    {
        $this->entityCollectionSetTest('child', 'LearnerGroup', 'getChildren', 'setChildren');
    }

    /**
     * @covers \App\Entity\LearnerGroup::setAncestor
     * @covers \App\Entity\LearnerGroup::getAncestor
     */
    public function testSetAncestor()
    {
        $this->entitySetTest('ancestor', 'LearnerGroup');
    }

    /**
     * @covers \App\Entity\LearnerGroup::getAncestorOrSelf
     */
    public function testGetAncestorOrSelfWithAncestor()
    {
        $ancestor = m::mock('App\Entity\LearnerGroup');
        $this->object->setAncestor($ancestor);
        $this->assertSame($ancestor, $this->object->getAncestorOrSelf());
    }

    /**
     * @covers \App\Entity\LearnerGroup::getAncestorOrSelf
     */
    public function testGetAncestorOrSelfWithNoAncestor()
    {
        $this->assertSame($this->object, $this->object->getAncestorOrSelf());
    }

    /**
     * @covers \App\Entity\LearnerGroup::addDescendant
     */
    public function testAddDescendant()
    {
        $this->entityCollectionAddTest('descendant', 'LearnerGroup', 'getDescendants', 'addDescendant', 'setAncestor');
    }

    /**
     * @covers \App\Entity\LearnerGroup::removeDescendant
     */
    public function testRemoveDescendant()
    {
        $this->entityCollectionRemoveTest('descendant', 'LearnerGroup');
    }

    /**
     * @covers \App\Entity\LearnerGroup::getDescendants
     * @covers \App\Entity\LearnerGroup::setDescendants
     */
    public function testGetDescendants()
    {
        $this->entityCollectionSetTest('descendant', 'LearnerGroup', 'getDescendants', 'setDescendants', 'setAncestor');
    }
}
