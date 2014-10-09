<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\CurriculumInventoryAcademicLevel;
use Mockery as m;

/**
 * Tests for Model CurriculumInventoryAcademicLevel
 */
class CurriculumInventoryAcademicLevelTest extends ModelBase
{
    /**
     * @var CurriculumInventoryAcademicLevel
     */
    protected $object;

    /**
     * Instantiate a CurriculumInventoryAcademicLevel object
     */
    protected function setUp()
    {
        $this->object = new CurriculumInventoryAcademicLevel;
    }
    

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryAcademicLevel::getAcademicLevelId
     */
    public function testGetAcademicLevelId()
    {
        $this->basicGetTest('academicLevelId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryAcademicLevel::setLevel
     */
    public function testSetLevel()
    {
        $this->basicSetTest('level', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryAcademicLevel::getLevel
     */
    public function testGetLevel()
    {
        $this->basicGetTest('level', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryAcademicLevel::setName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryAcademicLevel::getName
     */
    public function testGetName()
    {
        $this->basicGetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryAcademicLevel::setDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryAcademicLevel::getDescription
     */
    public function testGetDescription()
    {
        $this->basicGetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryAcademicLevel::setReport
     */
    public function testSetReport()
    {
        $this->modelSetTest('report', 'CurriculumInventoryReport');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryAcademicLevel::getReport
     */
    public function testGetReport()
    {
        $this->modelGetTest('report', 'CurriculumInventoryReport');
    }
}
