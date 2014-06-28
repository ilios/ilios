<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\CurriculumInventoryExport;
use Mockery as m;

/**
 * Tests for Entity CurriculumInventoryExport
 */
class CurriculumInventoryExportTest extends EntityBase
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
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryExport::setReportId
     */
    public function testSetReportId()
    {
        $this->basicSetTest('reportId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryExport::getReportId
     */
    public function testGetReportId()
    {
        $this->basicGetTest('reportId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryExport::setDocument
     */
    public function testSetDocument()
    {
        $this->basicSetTest('document', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryExport::getDocument
     */
    public function testGetDocument()
    {
        $this->basicGetTest('document', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryExport::setCreatedOn
     */
    public function testSetCreatedOn()
    {
        $this->basicSetTest('createdOn', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryExport::getCreatedOn
     */
    public function testGetCreatedOn()
    {
        $this->basicGetTest('createdOn', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryExport::setReport
     */
    public function testSetReport()
    {
        $this->entitySetTest('report', 'CurriculumInventoryReport');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryExport::getReport
     */
    public function testGetReport()
    {
        $this->entityGetTest('report', 'CurriculumInventoryReport');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryExport::setCreatedBy
     */
    public function testSetCreatedBy()
    {
        $this->entitySetTest('createdBy', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryExport::getCreatedBy
     */
    public function testGetCreatedBy()
    {
        $this->entityGetTest('createdBy', 'User');
    }
}
