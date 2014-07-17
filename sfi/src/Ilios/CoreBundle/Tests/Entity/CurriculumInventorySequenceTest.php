<?php
namespace Ilios\CoreBundle\Tests\Entity;


use Ilios\CoreBundle\Entity\CurriculumInventorySequence;
use Mockery as m;

/**
 * Tests for Entity CurriculumInventorySequence
 */
class CurriculumInventorySequenceTest extends EntityBase
{
    /**
     * @var CurriculumInventorySequence
     */
    protected $object;

    /**
     * Instantiate a CurriculumInventorySequence object
     */
    protected function setUp()
    {
        $this->object = new CurriculumInventorySequence;
    }
    

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequence::setReportId
     */
    public function testSetReportId()
    {
        $this->basicSetTest('reportId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequence::getReportId
     */
    public function testGetReportId()
    {
        $this->basicGetTest('reportId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequence::setDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequence::getDescription
     */
    public function testGetDescription()
    {
        $this->basicGetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequence::setReport
     */
    public function testSetReport()
    {
        $this->entitySetTest('report', 'CurriculumInventoryReport');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventorySequence::getReport
     */
    public function testGetReport()
    {
        $this->entityGetTest('report', 'CurriculumInventoryReport');
    }
}
