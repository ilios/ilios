<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\ReportPoValue;
use Mockery as m;

/**
 * Tests for Model ReportPoValue
 */
class ReportPoValueTest extends BaseModel
{
    /**
     * @var ReportPoValue
     */
    protected $object;

    /**
     * Instantiate a ReportPoValue object
     */
    protected function setUp()
    {
        $this->object = new ReportPoValue;
    }

    /**
     * @covers Ilios\CoreBundle\Model\ReportPoValue::setPrepositionalObjectTableRowId
     */
    public function testSetPrepositionalObjectTableRowId()
    {
        $this->basicSetTest('prepositionalObjectTableRowId', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ReportPoValue::getReportId
     */
    public function testGetReportId()
    {
        $this->basicGetTest('reportId', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ReportPoValue::setReportId
     */
    public function testSetReportId()
    {
        $this->basicSetTest('reportId', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ReportPoValue::getPrepositionalObjectTableRowId
     */
    public function testGetPrepositionalObjectTableRowId()
    {
        $this->basicGetTest('prepositionalObjectTableRowId', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ReportPoValue::setDeleted
     */
    public function testSetDeleted()
    {
        $this->basicSetTest('deleted', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ReportPoValue::getDeleted
     */
    public function testGetDeleted()
    {
        $this->basicGetTest('deleted', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ReportPoValue::setReport
     */
    public function testSetReport()
    {
        $this->modelSetTest('report', 'Report');
    }

    /**
     * @covers Ilios\CoreBundle\Model\ReportPoValue::getReport
     */
    public function testGetReport()
    {
        $this->modelGetTest('report', 'Report');
    }
}
