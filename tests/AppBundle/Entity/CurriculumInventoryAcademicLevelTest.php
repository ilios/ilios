<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\CurriculumInventoryAcademicLevel;
use Mockery as m;

/**
 * Tests for Entity CurriculumInventoryAcademicLevel
 */
class CurriculumInventoryAcademicLevelTest extends EntityBase
{
    /**
     * @var CurriculumInventoryAcademicLevel
     */
    protected $object;

    /**
     * Instantiate a CurriculumInventoryAcademicLevel object
     */
    protected function setUp()
    {
        $this->object = new CurriculumInventoryAcademicLevel;
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'name',
            'level'
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setName('50 char max name test');
        $this->object->setLevel(4);
        $this->validate(0);
    }

    /**
     * @covers \AppBundle\Entity\CurriculumInventoryAcademicLevel::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getSequenceBlocks());
    }

    /**
     * @covers \AppBundle\Entity\CurriculumInventoryAcademicLevel::setLevel
     * @covers \AppBundle\Entity\CurriculumInventoryAcademicLevel::getLevel
     */
    public function testSetLevel()
    {
        $this->basicSetTest('level', 'integer');
    }

    /**
     * @covers \AppBundle\Entity\CurriculumInventoryAcademicLevel::setName
     * @covers \AppBundle\Entity\CurriculumInventoryAcademicLevel::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \AppBundle\Entity\CurriculumInventoryAcademicLevel::setDescription
     * @covers \AppBundle\Entity\CurriculumInventoryAcademicLevel::getDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers \AppBundle\Entity\CurriculumInventoryAcademicLevel::setReport
     * @covers \AppBundle\Entity\CurriculumInventoryAcademicLevel::getReport
     */
    public function testSetReport()
    {
        $this->entitySetTest('report', 'CurriculumInventoryReport');
    }

    /**
     * @covers \AppBundle\Entity\CurriculumInventoryAcademicLevel::addSequenceBlock
     */
    public function testAddSequenceBlock()
    {
        $this->entityCollectionAddTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \AppBundle\Entity\CurriculumInventoryAcademicLevel::removeSequenceBlock
     */
    public function testRemoveSequenceBlock()
    {
        $this->entityCollectionRemoveTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \AppBundle\Entity\CurriculumInventoryAcademicLevel::getSequenceBlocks
     */
    public function testGetSequenceBlocks()
    {
        $this->entityCollectionSetTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }
}
