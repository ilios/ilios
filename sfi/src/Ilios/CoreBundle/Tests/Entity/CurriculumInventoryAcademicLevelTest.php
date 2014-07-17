<?php
namespace Ilios\CoreBundle\Tests\Entity;


use Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel;
use Mockery as m;

/**
 * Tests for Entity CurriculumInventoryAcademicLevel
 */
class CurriculumInventoryAcademicLevelTest extends EntityBase
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
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel::getAcademicLevelId
     */
    public function testGetAcademicLevelId()
    {
        $this->basicGetTest('academicLevelId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel::setLevel
     */
    public function testSetLevel()
    {
        $this->basicSetTest('level', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel::getLevel
     */
    public function testGetLevel()
    {
        $this->basicGetTest('level', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel::setName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel::getName
     */
    public function testGetName()
    {
        $this->basicGetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel::setDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel::getDescription
     */
    public function testGetDescription()
    {
        $this->basicGetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel::setReport
     */
    public function testSetReport()
    {
        $this->entitySetTest('report', 'CurriculumInventoryReport');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel::getReport
     */
    public function testGetReport()
    {
        $this->entityGetTest('report', 'CurriculumInventoryReport');
    }
}
