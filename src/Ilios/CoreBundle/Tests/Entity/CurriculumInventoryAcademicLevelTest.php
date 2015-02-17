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
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel::setLevel
     */
    public function testSetLevel()
    {
        $this->basicSetTest('level', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel::setName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel::setDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel::setReport
     */
    public function testSetReport()
    {
        $this->entitySetTest('report', 'CurriculumInventoryReport');
    }
}
