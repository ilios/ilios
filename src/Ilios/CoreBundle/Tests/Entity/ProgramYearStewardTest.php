<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\Program;
use Ilios\CoreBundle\Entity\ProgramYear;
use Ilios\CoreBundle\Entity\ProgramYearSteward;
use Ilios\CoreBundle\Entity\School;
use Mockery as m;

/**
 * Tests for Entity ProgramYearSteward
 */
class ProgramYearStewardTest extends EntityBase
{
    /**
     * @var ProgramYearSteward
     */
    protected $object;

    /**
     * Instantiate a ProgramYearSteward object
     */
    protected function setUp()
    {
        $this->object = new ProgramYearSteward;
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYearSteward::setDepartment
     * @covers Ilios\CoreBundle\Entity\ProgramYearSteward::getDepartment
     */
    public function testSetDepartment()
    {
        $this->entitySetTest('department', 'Department');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYearSteward::setProgramYear
     * @covers Ilios\CoreBundle\Entity\ProgramYearSteward::getProgramYear
     */
    public function testSetProgramYear()
    {
        $this->softDeleteEntitySetTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYearSteward::setSchool
     * @covers Ilios\CoreBundle\Entity\ProgramYearSteward::getSchool
     */
    public function testSetSchool()
    {
        $this->softDeleteEntitySetTest('school', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYearSteward::getSchool
     */
    public function testGetSchool()
    {
        $this->softDeleteEntitySetTest('school', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYearSteward::getProgram
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
     * @covers Ilios\CoreBundle\Entity\ProgramYearSteward::getProgramOwningSchool
     */
    public function testGetProgramOwningSchool()
    {
        $school = new School();
        $program = new Program();
        $program->setSchool($school);
        $programYear = new ProgramYear();
        $programYear->setProgram($program);
        $this->object->setProgramYear($programYear);

        $this->assertEquals($program, $this->object->getProgram());

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
