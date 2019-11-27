<?php
namespace App\Tests\Entity;

use App\Entity\Program;
use App\Entity\ProgramYear;
use App\Entity\ProgramYearSteward;
use App\Entity\School;
use Mockery as m;

/**
 * Tests for Entity ProgramYearSteward
 * @group model
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
     * @covers \App\Entity\ProgramYearSteward::setDepartment
     * @covers \App\Entity\ProgramYearSteward::getDepartment
     */
    public function testSetDepartment()
    {
        $this->entitySetTest('department', 'Department');
    }

    /**
     * @covers \App\Entity\ProgramYearSteward::setDepartment
     * @covers \App\Entity\ProgramYearSteward::getDepartment
     */
    public function testSetDepartmentToNull()
    {
        $this->object->setDepartment(null);
        $this->assertEquals(null, $this->object->getDepartment());
    }

    /**
     * @covers \App\Entity\ProgramYearSteward::setProgramYear
     * @covers \App\Entity\ProgramYearSteward::getProgramYear
     */
    public function testSetProgramYear()
    {
        $this->entitySetTest('programYear', 'ProgramYear');
    }

    /**
     * @covers \App\Entity\ProgramYearSteward::setSchool
     * @covers \App\Entity\ProgramYearSteward::getSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }

    /**
     * @covers \App\Entity\ProgramYearSteward::getSchool
     */
    public function testGetSchool()
    {
        $this->entitySetTest('school', 'School');
    }

    /**
     * @covers \App\Entity\ProgramYearSteward::getProgram
     */
    public function testGetProgram()
    {
        $program = new Program();
        $programYear = new ProgramYear();
        $programYear->setProgram($program);
        $pySteward = new ProgramYearSteward();
        $pySteward->setProgramYear($programYear);
        $this->assertEquals($program, $pySteward->getProgram());

        $programYear = new ProgramYear();
        $pySteward = new ProgramYearSteward();
        $pySteward->setProgramYear($programYear);
        $this->assertNull($pySteward->getProgram());

        $pySteward = new ProgramYearSteward();
        $this->assertNull($pySteward->getProgram());
    }

    /**
     * @covers \App\Entity\ProgramYearSteward::getProgramOwningSchool
     */
    public function testGetProgramOwningSchool()
    {
        $school = new School();
        $program = new Program();
        $program->setSchool($school);
        $programYear = new ProgramYear();
        $programYear->setProgram($program);
        $pySteward = new ProgramYearSteward();
        $pySteward->setProgramYear($programYear);
        $this->assertEquals($school, $pySteward->getProgramOwningSchool());

        $program = new Program();
        $programYear = new ProgramYear();
        $programYear->setProgram($program);
        $pySteward = new ProgramYearSteward();
        $pySteward->setProgramYear($programYear);
        $this->assertNull($pySteward->getProgramOwningSchool());

        $programYear = new ProgramYear();
        $pySteward = new ProgramYearSteward();
        $pySteward->setProgramYear($programYear);
        $this->assertNull($pySteward->getProgramOwningSchool());

        $pySteward = new ProgramYearSteward();
        $this->assertNull($pySteward->getProgramOwningSchool());
    }
}
