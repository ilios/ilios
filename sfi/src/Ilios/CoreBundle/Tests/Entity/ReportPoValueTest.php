<?php
namespace Ilios\CoreBundle\Tests\Entity;


use Ilios\CoreBundle\Entity\ReportPoValue;
use Mockery as m;

/**
 * Tests for Entity ReportPoValue
 */
class ReportPoValueTest extends EntityBase
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
     * @covers Ilios\CoreBundle\Entity\ReportPoValue::setPrepositionalObjectTableRowId
     */
    public function testSetPrepositionalObjectTableRowId()
    {
        $this->basicSetTest('prepositionalObjectTableRowId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ReportPoValue::getReportId
     */
    public function testGetReportId()
    {
        $this->basicGetTest('reportId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ReportPoValue::setReportId
     */
    public function testSetReportId()
    {
        $this->basicSetTest('reportId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ReportPoValue::getPrepositionalObjectTableRowId
     */
    public function testGetPrepositionalObjectTableRowId()
    {
        $this->basicGetTest('prepositionalObjectTableRowId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ReportPoValue::setDeleted
     */
    public function testSetDeleted()
    {
        $this->basicSetTest('deleted', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ReportPoValue::getDeleted
     */
    public function testGetDeleted()
    {
        $this->basicGetTest('deleted', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ReportPoValue::setReport
     */
    public function testSetReport()
    {
        $this->entitySetTest('report', 'Report');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ReportPoValue::getReport
     */
    public function testGetReport()
    {
        $this->entityGetTest('report', 'Report');
    }
}
