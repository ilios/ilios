<?php
namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\Cohort;
use Ilios\CoreBundle\Entity\Program;
use Ilios\CoreBundle\Entity\ProgramYear;
use Ilios\CoreBundle\Entity\School;
use Mockery as m;

/**
 * Tests for Entity Cohort
 */
class CohortTest extends EntityBase
{
    /**
     * @var Cohort
     */
    protected $object;

    /**
     * Instantiate a Cohort object
     */
    protected function setUp()
    {
        $this->object = new Cohort;
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'title'
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('up to sixty char');
        $this->validate(0);
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Cohort::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getCourses());
        $this->assertEmpty($this->object->getLearnerGroups());
        $this->assertEmpty($this->object->getUsers());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Cohort::setTitle
     * @covers \Ilios\CoreBundle\Entity\Cohort::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Cohort::setProgramYear
     * @covers \Ilios\CoreBundle\Entity\Cohort::getProgramYear
     */
    public function testSetProgramYear()
    {
        $this->entitySetTest('programYear', 'ProgramYear');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Cohort::addCourse
     */
    public function testAddCourse()
    {
        $this->entityCollectionAddTest('course', 'Course', false, false, 'addCohort');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Cohort::removeCourse
     */
    public function testRemoveCourse()
    {
        $this->entityCollectionRemoveTest('course', 'Course', false, false, false, 'removeCohort');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Cohort::getCourses
     */
    public function testGetCourses()
    {
        $this->entityCollectionSetTest('course', 'Course', false, false, 'addCohort');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Cohort::addUser
     */
    public function testAddUser()
    {
        $this->entityCollectionAddTest('user', 'User', false, false, 'addCohort');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Cohort::removeUser
     */
    public function testRemoveUser()
    {
        $this->entityCollectionRemoveTest('user', 'User', false, false, false, 'removeCohort');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Cohort::getUsers
     */
    public function testGetUsers()
    {
        $this->entityCollectionSetTest('user', 'User', false, false, 'addCohort');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Cohort::addLearnerGroup
     */
    public function testAddLearnerGroup()
    {
        $this->entityCollectionSetTest('learnerGroup', 'LearnerGroup');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Cohort::removeLearnerGroup
     */
    public function testRemoveLearnerGroup()
    {
        $this->entityCollectionSetTest('learnerGroup', 'LearnerGroup');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Cohort::getLearnerGroups
     */
    public function testGetLearnerGroups()
    {
        $this->entityCollectionSetTest('learnerGroup', 'LearnerGroup');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Cohort::getProgram
     */
    public function testGetProgram()
    {
        $program = new Program();
        $programYear = new ProgramYear();
        $programYear->setProgram($program);
        $cohort = new Cohort();
        $cohort->setProgramYear($programYear);
        $this->assertEquals($program, $cohort->getProgram());

        $programYear = new ProgramYear();
        $cohort = new Cohort();
        $cohort->setProgramYear($programYear);
        $this->assertNull($cohort->getProgram());

        $cohort = new Cohort();
        $this->assertNull($cohort->getProgram());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Cohort::getSchool
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
        $this->assertEquals($school, $cohort->getSchool());

        $program = new Program();
        $programYear = new ProgramYear();
        $programYear->setProgram($program);
        $cohort = new Cohort();
        $cohort->setProgramYear($programYear);
        $this->assertNull($cohort->getSchool());

        $programYear = new ProgramYear();
        $cohort = new Cohort();
        $cohort->setProgramYear($programYear);
        $this->assertNull($cohort->getSchool());

        $cohort = new Cohort();
        $this->assertNull($cohort->getSchool());
    }
}
