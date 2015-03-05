<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\Discipline;
use Mockery as m;

/**
 * Tests for Entity Discipline
 */
class DisciplineTest extends EntityBase
{
    /**
     * @var Discipline
     */
    protected $object;

    /**
     * Instantiate a Discipline object
     */
    protected function setUp()
    {
        $this->object = new Discipline;
    }

    // NotBlank() Validation not necessary - no NotBlank() fields apply

    /**
     * @covers Ilios\CoreBundle\Entity\Discipline::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getCourses());
        $this->assertEmpty($this->object->getProgramYears());
        $this->assertEmpty($this->object->getSessions());
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Discipline::setTitle
     * @covers Ilios\CoreBundle\Entity\Discipline::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Discipline::setOwningSchool
     * @covers Ilios\CoreBundle\Entity\Discipline::getOwningSchool
     */
    public function testSetOwningSchool()
    {
        $this->entitySetTest('owningSchool', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Discipline::addCourse
     */
    public function testAddCourse()
    {
        $this->entityCollectionAddTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Discipline::getCourses
     */
    public function testGetCourses()
    {
        $this->entityCollectionSetTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Discipline::addProgramYear
     */
    public function testAddProgramYear()
    {
        $this->entityCollectionAddTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Discipline::getProgramYears
     */
    public function testGetProgramYears()
    {
        $this->entityCollectionSetTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Discipline::addSession
     */
    public function testAddSession()
    {
        $this->entityCollectionAddTest('session', 'Session');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Discipline::getSessions
     */
    public function testGetSessions()
    {
        $this->entityCollectionSetTest('session', 'Session');
    }
}
