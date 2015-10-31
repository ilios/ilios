<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\Cohort;
use Ilios\CoreBundle\Entity\Program;
use Ilios\CoreBundle\Entity\ProgramYear;
use Ilios\CoreBundle\Entity\School;
use Mockery as m;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @covers Ilios\CoreBundle\Entity\Cohort::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getCourses());
        $this->assertEmpty($this->object->getLearnerGroups());
        $this->assertEmpty($this->object->getUsers());
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Cohort::setTitle
     * @covers Ilios\CoreBundle\Entity\Cohort::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Cohort::setProgramYear
     * @covers Ilios\CoreBundle\Entity\Cohort::getProgramYear
     */
    public function testSetProgramYear()
    {
        $this->softDeleteEntitySetTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Cohort::addCourse
     */
    public function testAddCourse()
    {
        $this->softDeleteEntityCollectionAddTest('course', 'Course', false, false, 'addCohort');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Cohort::getCourses
     */
    public function testGetCourses()
    {
        $this->softDeleteEntityCollectionSetTest('course', 'Course', false, false, 'addCohort');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Cohort::getProgram
     */
    public function testGetProgram()
    {
        $program = new Program();
        $programYear = new ProgramYear();
        $programYear->setProgram($program);
        $this->object->setProgramYear($programYear);
        $this->assertEquals($program, $this->object->getProgram());

        $program->setDeleted(true);
        $this->assertNull($this->object->getProgram());

        $program->setDeleted(false);
        $programYear->setDeleted(true);
        $this->assertNull($this->object->getProgram());
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Cohort::getSchool
     */
    public function testGetSchool()
    {
        $school = new School();
        $program = new Program();
        $program->setSchool($school);
        $programYear = new ProgramYear();
        $programYear->setProgram($program);
        $this->object->setProgramYear($programYear);
        $this->assertEquals($school, $this->object->getSchool());

        $school->setDeleted(true);
        $this->assertNull($this->object->getSchool());

        $school->setDeleted(false);
        $program->setDeleted(true);
        $this->assertNull($this->object->getSchool());

        $program->setDeleted(false);
        $programYear->setDeleted(true);
        $this->assertNull($this->object->getSchool());
    }
}
