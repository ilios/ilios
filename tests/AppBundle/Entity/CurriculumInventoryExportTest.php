<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\CurriculumInventoryExport;
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
     * @covers \AppBundle\Entity\Session::__construct
     */
    public function testConstructor()
    {
        $this->assertNotEmpty($this->object->getCreatedAt());
    }

    /**
     * @covers \AppBundle\Entity\CurriculumInventoryExport::setDocument
     * @covers \AppBundle\Entity\CurriculumInventoryExport::getDocument
     */
    public function testSetDocument()
    {
        $this->basicSetTest('document', 'string');
    }

    /**
     * @covers \AppBundle\Entity\CurriculumInventoryExport::setReport
     * @covers \AppBundle\Entity\CurriculumInventoryExport::getReport
     */
    public function testSetReport()
    {
        $this->entitySetTest('report', 'CurriculumInventoryReport');
    }

    /**
     * @covers \AppBundle\Entity\CurriculumInventoryExport::setCreatedBy
     * @covers \AppBundle\Entity\CurriculumInventoryExport::getCreatedBy
     */
    public function testSetCreatedBy()
    {
        $this->entitySetTest('createdBy', 'User');
    }
}
