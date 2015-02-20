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
     * @covers Ilios\CoreBundle\Entity\ReportPoValue::setDeleted
     * @covers Ilios\CoreBundle\Entity\ReportPoValue::isDeleted
     */
    public function testSetDeleted()
    {
        $this->booleanSetTest('deleted');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ReportPoValue::setReport
     * @covers Ilios\CoreBundle\Entity\ReportPoValue::getReport
     */
    public function testSetReport()
    {
        $this->entitySetTest('report', 'Report');
    }
}
