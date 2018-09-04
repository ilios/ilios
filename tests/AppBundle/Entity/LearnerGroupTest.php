<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Cohort;
use AppBundle\Entity\LearnerGroup;
use AppBundle\Entity\Program;
use AppBundle\Entity\ProgramYear;
use AppBundle\Entity\School;
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
        $this->object->setCohort(m::mock('AppBundle\Entity\CohortInterface'));

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

        $this->object->setCohort(m::mock('AppBundle\Entity\CohortInterface'));

        $this->validate(0);
    }

    /**
     * @covers \AppBundle\Entity\LearnerGroup::__construct
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
     * @covers \AppBundle\Entity\LearnerGroup::setTitle
     * @covers \AppBundle\Entity\LearnerGroup::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \AppBundle\Entity\LearnerGroup::setLocation
     * @covers \AppBundle\Entity\LearnerGroup::getLocation
     */
    public function testSetLocation()
    {
        $this->basicSetTest('location', 'string');
    }

    /**
     * @covers \AppBundle\Entity\LearnerGroup::setCohort
     * @covers \AppBundle\Entity\LearnerGroup::getCohort
     */
    public function testSetCohort()
    {
        $this->entitySetTest('cohort', 'Cohort');
    }

    /**
     * @covers \AppBundle\Entity\LearnerGroup::addIlmSession
     */
    public function testAddIlmSession()
    {
        $this->entityCollectionAddTest('ilmSession', 'IlmSession', false, false, 'addLearnerGroup');
    }

    /**
     * @covers \AppBundle\Entity\LearnerGroup::removeIlmSession
     */
    public function testRemoveIlmSession()
    {
        $this->entityCollectionRemoveTest('ilmSession', 'IlmSession', false, false, false, 'removeLearnerGroup');
    }

    /**
     * @covers \AppBundle\Entity\LearnerGroup::getIlmSessions
     */
    public function getGetIlmSessions()
    {
        $this->entityCollectionSetTest('ilmSession', 'IlmSession', false, false, 'addLearnerGroup');
    }

    /**
     * @covers \AppBundle\Entity\LearnerGroup::addOffering
     */
    public function testAddOffering()
    {
        $this->entityCollectionAddTest('offering', 'Offering', false, false, 'addLearnerGroup');
    }

    /**
     * @covers \AppBundle\Entity\LearnerGroup::removeOffering
     */
    public function testRemoveOffering()
    {
        $this->entityCollectionRemoveTest('offering', 'Offering', false, false, false, 'removeLearnerGroup');
    }

    /**
     * @covers \AppBundle\Entity\LearnerGroup::getOfferings
     */
    public function getGetOfferings()
    {
        $this->entityCollectionSetTest('offering', 'Offering', false, false, 'addLearnerGroup');
    }

    /**
     * @covers \AppBundle\Entity\LearnerGroup::addInstructorGroup
     */
    public function testAddInstructorGroup()
    {
        $this->entityCollectionAddTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \AppBundle\Entity\LearnerGroup::removeInstructorGroup
     */
    public function testRemoveInstructorGroup()
    {
        $this->entityCollectionRemoveTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \AppBundle\Entity\LearnerGroup::getInstructorGroups
     */
    public function getGetInstructorGroups()
    {
        $this->entityCollectionSetTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers \AppBundle\Entity\LearnerGroup::addUser
     */
    public function testAddUser()
    {
        $this->entityCollectionAddTest('user', 'User');
    }

    /**
     * @covers \AppBundle\Entity\LearnerGroup::removeUser
     */
    public function testRemoveUser()
    {
        $this->entityCollectionRemoveTest('user', 'User');
    }

    /**
     * @covers \AppBundle\Entity\LearnerGroup::getUsers
     */
    public function getGetUsers()
    {
        $this->entityCollectionSetTest('user', 'User');
    }

    /**
     * @covers \AppBundle\Entity\LearnerGroup::addInstructor
     */
    public function testAddInstructor()
    {
        $this->entityCollectionAddTest('instructor', 'User');
    }

    /**
     * @covers \AppBundle\Entity\LearnerGroup::removeInstructor
     */
    public function testRemoveInstructor()
    {
        $this->entityCollectionRemoveTest('instructor', 'User');
    }

    /**
     * @covers \AppBundle\Entity\LearnerGroup::getInstructors
     */
    public function getGetInstructors()
    {
        $this->entityCollectionSetTest('instructor', 'User');
    }

    /**
     * @covers \AppBundle\Entity\LearnerGroup::getProgramYear
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
     * @covers \AppBundle\Entity\LearnerGroup::getProgram
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
     * @covers \AppBundle\Entity\LearnerGroup::getSchool
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
     * @covers \AppBundle\Entity\LearnerGroup::addChild
     */
    public function testAddChild()
    {
        $this->entityCollectionAddTest('child', 'LearnerGroup', 'getChildren');
    }

    /**
     * @covers \AppBundle\Entity\LearnerGroup::removeChild
     */
    public function testRemoveChild()
    {
        $this->entityCollectionRemoveTest('child', 'LearnerGroup', 'getChildren');
    }

    /**
     * @covers \AppBundle\Entity\LearnerGroup::getChildren
     * @covers \AppBundle\Entity\LearnerGroup::setChildren
     */
    public function testGetChildren()
    {
        $this->entityCollectionSetTest('child', 'LearnerGroup', 'getChildren', 'setChildren');
    }

    /**
     * @covers \AppBundle\Entity\LearnerGroup::setAncestor
     * @covers \AppBundle\Entity\LearnerGroup::getAncestor
     */
    public function testSetAncestor()
    {
        $this->entitySetTest('ancestor', 'LearnerGroup');
    }

    /**
     * @covers \AppBundle\Entity\LearnerGroup::getAncestorOrSelf
     */
    public function testGetAncestorOrSelfWithAncestor()
    {
        $ancestor = m::mock('AppBundle\Entity\LearnerGroup');
        $this->object->setAncestor($ancestor);
        $this->assertSame($ancestor, $this->object->getAncestorOrSelf());
    }

    /**
     * @covers \AppBundle\Entity\LearnerGroup::getAncestorOrSelf
     */
    public function testGetAncestorOrSelfWithNoAncestor()
    {
        $this->assertSame($this->object, $this->object->getAncestorOrSelf());
    }

    /**
     * @covers \AppBundle\Entity\LearnerGroup::addDescendant
     */
    public function testAddDescendant()
    {
        $this->entityCollectionAddTest('descendant', 'LearnerGroup', 'getDescendants', 'addDescendant', 'setAncestor');
    }

    /**
     * @covers \AppBundle\Entity\LearnerGroup::removeDescendant
     */
    public function testRemoveDescendant()
    {
        $this->entityCollectionRemoveTest('descendant', 'LearnerGroup');
    }

    /**
     * @covers \AppBundle\Entity\LearnerGroup::getDescendants
     * @covers \AppBundle\Entity\LearnerGroup::setDescendants
     */
    public function testGetDescendants()
    {
        $this->entityCollectionSetTest('descendant', 'LearnerGroup', 'getDescendants', 'setDescendants', 'setAncestor');
    }
}
