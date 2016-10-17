<?php
namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\Department;
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
        $this->object->setSchool(m::mock('Ilios\CoreBundle\Entity\SchoolInterface'));
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

        $this->object->setSchool(m::mock('Ilios\CoreBundle\Entity\SchoolInterface'));

        $this->validate(0);
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Department::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getStewards());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Department::setTitle
     * @covers \Ilios\CoreBundle\Entity\Department::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Department::setSchool
     * @covers \Ilios\CoreBundle\Entity\Department::getSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::addSteward
     */
    public function testAddSteward()
    {
        $this->entityCollectionAddTest('steward', 'ProgramYearSteward');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::removeSteward
     */
    public function testRemoveSteward()
    {
        $this->entityCollectionRemoveTest('steward', 'ProgramYearSteward');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\School::getStewards
     */
    public function testGetSteward()
    {
        $this->entityCollectionSetTest('steward', 'ProgramYearSteward');
    }
}
