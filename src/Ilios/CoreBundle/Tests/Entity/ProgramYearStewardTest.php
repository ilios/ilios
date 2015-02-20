<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\ProgramYearSteward;
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
        $this->entitySetTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYearSteward::getProgramYear
     */
    public function testGetProgramYear()
    {
        $this->entitySetTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYearSteward::setSchool
     * @covers Ilios\CoreBundle\Entity\ProgramYearSteward::getSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYearSteward::getSchool
     */
    public function testGetSchool()
    {
        $this->entitySetTest('school', 'School');
    }
}
