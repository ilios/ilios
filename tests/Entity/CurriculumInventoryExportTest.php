<?php
namespace App\Tests\Entity;

use App\Entity\CurriculumInventoryExport;
use Mockery as m;

/**
 * Tests for Entity CurriculumInventoryExport
 * @group model
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

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'document',
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setDocument('text file super large test');
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\Session::__construct
     */
    public function testConstructor()
    {
        $this->assertNotEmpty($this->object->getCreatedAt());
    }

    /**
     * @covers \App\Entity\CurriculumInventoryExport::setDocument
     * @covers \App\Entity\CurriculumInventoryExport::getDocument
     */
    public function testSetDocument()
    {
        $this->basicSetTest('document', 'string');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryExport::setReport
     * @covers \App\Entity\CurriculumInventoryExport::getReport
     */
    public function testSetReport()
    {
        $this->entitySetTest('report', 'CurriculumInventoryReport');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryExport::setCreatedBy
     * @covers \App\Entity\CurriculumInventoryExport::getCreatedBy
     */
    public function testSetCreatedBy()
    {
        $this->entitySetTest('createdBy', 'User');
    }
}
