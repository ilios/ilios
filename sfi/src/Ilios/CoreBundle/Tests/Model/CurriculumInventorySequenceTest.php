<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\CurriculumInventorySequence;
use Mockery as m;

/**
 * Tests for Model CurriculumInventorySequence
 */
class CurriculumInventorySequenceTest extends BaseModel
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
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequence::setReportId
     */
    public function testSetReportId()
    {
        $this->basicSetTest('reportId', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequence::getReportId
     */
    public function testGetReportId()
    {
        $this->basicGetTest('reportId', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequence::setDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequence::getDescription
     */
    public function testGetDescription()
    {
        $this->basicGetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequence::setReport
     */
    public function testSetReport()
    {
        $this->modelSetTest('report', 'CurriculumInventoryReport');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventorySequence::getReport
     */
    public function testGetReport()
    {
        $this->modelGetTest('report', 'CurriculumInventoryReport');
    }
}
