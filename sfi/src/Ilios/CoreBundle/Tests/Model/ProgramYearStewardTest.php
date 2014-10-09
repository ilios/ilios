<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\ProgramYearSteward;
use Mockery as m;

/**
 * Tests for Model ProgramYearSteward
 */
class ProgramYearStewardTest extends ModelBase
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
     * @covers Ilios\CoreBundle\Model\ProgramYearSteward::getProgramYearStewardId
     */
    public function testGetProgramYearStewardId()
    {
        $this->basicGetTest('programYearStewardId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ProgramYearSteward::setDepartment
     */
    public function testSetDepartment()
    {
        $this->modelSetTest('department', 'Department');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ProgramYearSteward::getDepartment
     */
    public function testGetDepartment()
    {
        $this->modelGetTest('department', 'Department');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ProgramYearSteward::setProgramYear
     */
    public function testSetProgramYear()
    {
        $this->modelSetTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ProgramYearSteward::getProgramYear
     */
    public function testGetProgramYear()
    {
        $this->modelSetTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ProgramYearSteward::setSchool
     */
    public function testSetSchool()
    {
        $this->modelSetTest('school', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ProgramYearSteward::getSchool
     */
    public function testGetSchool()
    {
        $this->modelSetTest('school', 'School');
    }
}
