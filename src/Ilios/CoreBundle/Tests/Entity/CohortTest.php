<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\Cohort;
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

    /**
     * @covers Ilios\CoreBundle\Entity\Cohort::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getCourses());
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Cohort::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Cohort::setProgramYear
     */
    public function testSetProgramYear()
    {
        $this->entitySetTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Cohort::addCourse
     */
    public function testAddCourse()
    {
        $this->entityCollectionAddTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Cohort::removeCourse
     */
    public function testRemoveCourse()
    {
        $this->entityCollectionRemoveTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Cohort::getCourses
     */
    public function testGetCourses()
    {
        $this->entityCollectionSetTest('course', 'Course');
    }
}
