<?php
namespace Ilios\CoreBundle\Tests\Model;

use Ilios\CoreBundle\Model\Cohort;
use Mockery as m;

/**
 * Tests for Model Cohort
 */
class CohortTest extends ModelBase
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

    /**
     * @covers Ilios\CoreBundle\Model\Cohort::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getCourses());
    }
    
    /**
     * @covers Ilios\CoreBundle\Model\Cohort::getCohortId
     */
    public function testGetCohortId()
    {
        $this->basicGetTest('cohortId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Cohort::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Cohort::getTitle
     */
    public function testGetTitle()
    {
        $this->basicGetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Cohort::setProgramYear
     */
    public function testSetProgramYear()
    {
        $this->modelSetTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Cohort::getProgramYear
     */
    public function testGetProgramYear()
    {
        $this->modelGetTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Cohort::addCourse
     */
    public function testAddCourse()
    {
        $this->modelCollectionAddTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Cohort::removeCourse
     */
    public function testRemoveCourse()
    {
        $this->modelCollectionRemoveTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Cohort::getCourses
     */
    public function testGetCourses()
    {
        $this->modelCollectionGetTest('course', 'Course');
    }
}
