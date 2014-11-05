<?php
namespace Ilios\CoreBundle\Tests\Model;

use Ilios\CoreBundle\Model\CurriculumInventoryExport;
use Mockery as m;

/**
 * Tests for Model CurriculumInventoryExport
 */
class CurriculumInventoryExportTest extends BaseModel
{
    /**
     * @var CurriculumInventoryExport
     */
    protected $object;

    /**
     * Instantiate a CurriculumInventoryExport object
     */
    protected function setUp()
    {
        $this->object = new CurriculumInventoryExport;
    }
    

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryExport::setReportId
     */
    public function testSetReportId()
    {
        $this->basicSetTest('reportId', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryExport::getReportId
     */
    public function testGetReportId()
    {
        $this->basicGetTest('reportId', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryExport::setDocument
     */
    public function testSetDocument()
    {
        $this->basicSetTest('document', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryExport::getDocument
     */
    public function testGetDocument()
    {
        $this->basicGetTest('document', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryExport::setCreatedOn
     */
    public function testSetCreatedOn()
    {
        $this->basicSetTest('createdOn', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryExport::getCreatedOn
     */
    public function testGetCreatedOn()
    {
        $this->basicGetTest('createdOn', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryExport::setReport
     */
    public function testSetReport()
    {
        $this->modelSetTest('report', 'CurriculumInventoryReport');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryExport::getReport
     */
    public function testGetReport()
    {
        $this->modelGetTest('report', 'CurriculumInventoryReport');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryExport::setCreatedBy
     */
    public function testSetCreatedBy()
    {
        $this->modelSetTest('createdBy', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryExport::getCreatedBy
     */
    public function testGetCreatedBy()
    {
        $this->modelGetTest('createdBy', 'User');
    }
}
