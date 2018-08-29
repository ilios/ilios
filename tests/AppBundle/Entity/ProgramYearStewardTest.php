<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Program;
use AppBundle\Entity\ProgramYear;
use AppBundle\Entity\ProgramYearSteward;
use AppBundle\Entity\School;
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
     * @covers \AppBundle\Entity\ProgramYearSteward::setDepartment
     * @covers \AppBundle\Entity\ProgramYearSteward::getDepartment
     */
    public function testSetDepartment()
    {
        $this->entitySetTest('department', 'Department');
    }

    /**
     * @covers \AppBundle\Entity\ProgramYearSteward::setProgramYear
     * @covers \AppBundle\Entity\ProgramYearSteward::getProgramYear
     */
    public function testSetProgramYear()
    {
        $this->entitySetTest('programYear', 'ProgramYear');
    }

    /**
     * @covers \AppBundle\Entity\ProgramYearSteward::setSchool
     * @covers \AppBundle\Entity\ProgramYearSteward::getSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }

    /**
     * @covers \AppBundle\Entity\ProgramYearSteward::getSchool
     */
    public function testGetSchool()
    {
        $this->entitySetTest('school', 'School');
    }

    /**
     * @covers \AppBundle\Entity\ProgramYearSteward::getProgram
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
     * @covers \AppBundle\Entity\ProgramYearSteward::getProgramOwningSchool
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
