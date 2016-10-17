<?php
namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\Cohort;
use Ilios\CoreBundle\Entity\LearnerGroup;
use Ilios\CoreBundle\Entity\Program;
use Ilios\CoreBundle\Entity\ProgramYear;
use Ilios\CoreBundle\Entity\School;
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
        $this->object->setCohort(m::mock('Ilios\CoreBundle\Entity\CohortInterface'));

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

        $this->object->setCohort(m::mock('Ilios\CoreBundle\Entity\CohortInterface'));

        $this->validate(0);
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearnerGroup::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getIlmSessions());
        $this->assertEmpty($this->object->getInstructorGroups());
        $this->assertEmpty($this->object->getInstructors());
        $this->assertEmpty($this->object->getOfferings());
        $this->assertEmpty($this->object->getUsers());
        $this->assertEmpty($this->object->getChildren());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearnerGroup::setTitle
     * @covers \Ilios\CoreBundle\Entity\LearnerGroup::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearnerGroup::setLocation
     * @covers \Ilios\CoreBundle\Entity\LearnerGroup::getLocation
     */
    public function testSetLocation()
    {
        $this->basicSetTest('location', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearnerGroup::setCohort
     * @covers \Ilios\CoreBundle\Entity\LearnerGroup::getCohort
     */
    public function testSetCohort()
    {
        $this->entitySetTest('cohort', 'Cohort');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearnerGroup::addIlmSession
     */
    public function testAddIlmSession()
    {
        $this->entityCollectionAddTest('ilmSession', 'IlmSession', false, false, 'addLearnerGroup');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearnerGroup::removeIlmSession
     */
    public function testRemoveIlmSession()
    {
        $this->entityCollectionRemoveTest('ilmSession', 'IlmSession', false, false, false, 'removeLearnerGroup');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearnerGroup::getIlmSessions
     */
    public function getGetIlmSessions()
    {
        $this->entityCollectionSetTest('ilmSession', 'IlmSession', false, false, 'addLearnerGroup');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearnerGroup::addOffering
     */
    public function testAddOffering()
    {
        $this->entityCollectionAddTest('offering', 'Offering', false, false, 'addLearnerGroup');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearnerGroup::removeOffering
     */
    public function testRemoveOffering()
    {
        $this->entityCollectionRemoveTest('offering', 'Offering', false, false, false, 'removeLearnerGroup');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearnerGroup::getOfferings
     */
    public function getGetOfferings()
    {
        $this->entityCollectionSetTest('offering', 'Offering', false, false, 'addLearnerGroup');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearnerGroup::addInstructorGroup
     */
    public function testAddInstructorGroup()
    {
        $this->entityCollectionAddTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearnerGroup::removeInstructorGroup
     */
    public function testRemoveInstructorGroup()
    {
        $this->entityCollectionRemoveTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearnerGroup::getInstructorGroups
     */
    public function getGetInstructorGroups()
    {
        $this->entityCollectionSetTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearnerGroup::addUser
     */
    public function testAddUser()
    {
        $this->entityCollectionAddTest('user', 'User');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearnerGroup::removeUser
     */
    public function testRemoveUser()
    {
        $this->entityCollectionRemoveTest('user', 'User');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearnerGroup::getUsers
     */
    public function getGetUsers()
    {
        $this->entityCollectionSetTest('user', 'User');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearnerGroup::addInstructor
     */
    public function testAddInstructor()
    {
        $this->entityCollectionAddTest('instructor', 'User');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearnerGroup::removeInstructor
     */
    public function testRemoveInstructor()
    {
        $this->entityCollectionRemoveTest('instructor', 'User');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearnerGroup::getInstructors
     */
    public function getGetInstructors()
    {
        $this->entityCollectionSetTest('instructor', 'User');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearnerGroup::getProgramYear
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
     * @covers \Ilios\CoreBundle\Entity\LearnerGroup::getProgram
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
     * @covers \Ilios\CoreBundle\Entity\LearnerGroup::getSchool
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
     * @covers \Ilios\CoreBundle\Entity\LearnerGroup::addChild
     */
    public function testAddChild()
    {
        $this->entityCollectionAddTest('child', 'LearnerGroup', 'getChildren');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearnerGroup::removeChild
     */
    public function testRemoveChild()
    {
        $this->entityCollectionRemoveTest('child', 'LearnerGroup', 'getChildren');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\LearnerGroup::getChildren
     * @covers \Ilios\CoreBundle\Entity\LearnerGroup::setChildren
     */
    public function testGetChildren()
    {
        $this->entityCollectionSetTest('child', 'LearnerGroup', 'getChildren', 'setChildren');
    }
}
