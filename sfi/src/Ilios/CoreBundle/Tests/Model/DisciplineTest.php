<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\Discipline;
use Mockery as m;

/**
 * Tests for Model Discipline
 */
class DisciplineTest extends BaseModel
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
     * @covers Ilios\CoreBundle\Model\Discipline::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getCourses());
        $this->assertEmpty($this->object->getProgramYears());
        $this->assertEmpty($this->object->getSessions());
    }
    
    /**
     * @covers Ilios\CoreBundle\Model\Discipline::getDisciplineId
     */
    public function testGetDisciplineId()
    {
        $this->basicGetTest('disciplineId', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Discipline::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Discipline::getTitle
     */
    public function testGetTitle()
    {
        $this->basicGetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Discipline::setOwningSchool
     */
    public function testSetOwningSchool()
    {
        $this->modelSetTest('owningSchool', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Discipline::getOwningSchool
     */
    public function testGetOwngingSchool()
    {
        $this->modelGetTest('owningSchool', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Discipline::addCourse
     */
    public function testAddCourse()
    {
        $this->modelCollectionAddTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Discipline::removeCourse
     */
    public function testRemoveCourse()
    {
        $this->modelCollectionRemoveTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Discipline::getCourses
     */
    public function testGetCourses()
    {
        $this->modelCollectionGetTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Discipline::addProgramYear
     */
    public function testAddProgramYear()
    {
        $this->modelCollectionAddTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Discipline::removeProgramYear
     */
    public function testRemoveProgramYear()
    {
        $this->modelCollectionRemoveTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Discipline::getProgramYears
     */
    public function testGetProgramYears()
    {
        $this->modelCollectionGetTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Discipline::addSession
     */
    public function testAddSession()
    {
        $this->modelCollectionAddTest('session', 'Session');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Discipline::removeSession
     */
    public function testRemoveSession()
    {
        $this->modelCollectionRemoveTest('session', 'Session');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Discipline::getSessions
     */
    public function testGetSessions()
    {
        $this->modelCollectionGetTest('session', 'Session');
    }
}
