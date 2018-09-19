<?php
namespace Tests\App\Entity;

use App\Entity\Department;
use Mockery as m;

/**
 * Tests for Entity Department
 */
class DepartmentTest extends EntityBase
{
    /**
     * @var Department
     */
    protected $object;

    /**
     * Instantiate a Department object
     */
    protected function setUp()
    {
        $this->object = new Department;
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'title'
        );
        $this->object->setSchool(m::mock('App\Entity\SchoolInterface'));
        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('test');
        $this->validate(0);
    }

    public function testNotNullValidation()
    {
        $notNull = array(
            'school'
        );
        $this->object->setTitle('test');
        $this->validateNotNulls($notNull);

        $this->object->setSchool(m::mock('App\Entity\SchoolInterface'));

        $this->validate(0);
    }

    /**
     * @covers \App\Entity\Department::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getStewards());
    }

    /**
     * @covers \App\Entity\Department::setTitle
     * @covers \App\Entity\Department::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\Department::setSchool
     * @covers \App\Entity\Department::getSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }

    /**
     * @covers \App\Entity\School::addSteward
     */
    public function testAddSteward()
    {
        $this->entityCollectionAddTest('steward', 'ProgramYearSteward');
    }

    /**
     * @covers \App\Entity\School::removeSteward
     */
    public function testRemoveSteward()
    {
        $this->entityCollectionRemoveTest('steward', 'ProgramYearSteward');
    }

    /**
     * @covers \App\Entity\School::getStewards
     */
    public function testGetSteward()
    {
        $this->entityCollectionSetTest('steward', 'ProgramYearSteward');
    }
}
