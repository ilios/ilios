<?php
namespace Tests\CoreBundle\Entity;

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
     * @covers \Ilios\CoreBundle\Entity\Session::__construct
     */
    public function testConstructor()
    {
        $this->assertNotEmpty($this->object->getCreatedAt());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryExport::setDocument
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryExport::getDocument
     */
    public function testSetDocument()
    {
        $this->basicSetTest('document', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryExport::setReport
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryExport::getReport
     */
    public function testSetReport()
    {
        $this->entitySetTest('report', 'CurriculumInventoryReport');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryExport::setCreatedBy
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryExport::getCreatedBy
     */
    public function testSetCreatedBy()
    {
        $this->entitySetTest('createdBy', 'User');
    }
}
