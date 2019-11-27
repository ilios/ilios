<?php
namespace App\Tests\Entity;

use App\Entity\CurriculumInventoryAcademicLevel;
use Mockery as m;

/**
 * Tests for Entity CurriculumInventoryAcademicLevel
 * @group model
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
     * @covers \App\Entity\CurriculumInventoryAcademicLevel::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getSequenceBlocks());
    }

    /**
     * @covers \App\Entity\CurriculumInventoryAcademicLevel::setLevel
     * @covers \App\Entity\CurriculumInventoryAcademicLevel::getLevel
     */
    public function testSetLevel()
    {
        $this->basicSetTest('level', 'integer');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryAcademicLevel::setName
     * @covers \App\Entity\CurriculumInventoryAcademicLevel::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryAcademicLevel::setDescription
     * @covers \App\Entity\CurriculumInventoryAcademicLevel::getDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryAcademicLevel::setReport
     * @covers \App\Entity\CurriculumInventoryAcademicLevel::getReport
     */
    public function testSetReport()
    {
        $this->entitySetTest('report', 'CurriculumInventoryReport');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryAcademicLevel::addSequenceBlock
     */
    public function testAddSequenceBlock()
    {
        $this->entityCollectionAddTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryAcademicLevel::removeSequenceBlock
     */
    public function testRemoveSequenceBlock()
    {
        $this->entityCollectionRemoveTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryAcademicLevel::getSequenceBlocks
     */
    public function testGetSequenceBlocks()
    {
        $this->entityCollectionSetTest('sequenceBlock', 'CurriculumInventorySequenceBlock');
    }
}
