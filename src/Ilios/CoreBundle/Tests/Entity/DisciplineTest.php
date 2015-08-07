<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\Discipline;
use Mockery as m;
use Doctrine\Common\Collections\ArrayCollection;

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
        $this->softDeleteEntitySetTest('owningSchool', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Discipline::addCourse
     */
    public function testAddCourse()
    {
        $this->softDeleteEntityCollectionAddTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Discipline::getCourses
     */
    public function testGetCourses()
    {
        $this->softDeleteEntityCollectionSetTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Discipline::addProgramYear
     */
    public function testAddProgramYear()
    {
        $this->softDeleteEntityCollectionAddTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Discipline::getProgramYears
     */
    public function testGetProgramYears()
    {
        $this->softDeleteEntityCollectionSetTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Discipline::addSession
     */
    public function testAddSession()
    {
        $this->softDeleteEntityCollectionAddTest('session', 'Session');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Discipline::getSessions
     */
    public function testGetSessions()
    {
        $this->softDeleteEntityCollectionSetTest('session', 'Session');
    }
}
