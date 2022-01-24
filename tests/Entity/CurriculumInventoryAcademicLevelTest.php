<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\CurriculumInventoryAcademicLevel;

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
    protected function setUp(): void
    {
        $this->object = new CurriculumInventoryAcademicLevel();
    }

    public function testNotBlankValidation()
    {
        $notBlank = [
            'name',
            'level'
        ];
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
        $this->assertEmpty($this->object->getStartingSequenceBlocks());
        $this->assertEmpty($this->object->getEndingSequenceBlocks());
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
     * @covers \App\Entity\CurriculumInventoryAcademicLevel::addStartingSequenceBlock
     */
    public function testAddSequenceBlock()
    {
        $this->entityCollectionAddTest('startingSequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryAcademicLevel::removeStartingSequenceBlock
     */
    public function testRemoveSequenceBlock()
    {
        $this->entityCollectionRemoveTest('startingSequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryAcademicLevel::getStartingSequenceBlocks
     * @covers \App\Entity\CurriculumInventoryAcademicLevel::setStartingSequenceBlocks
     */
    public function testGetSequenceBlocks()
    {
        $this->entityCollectionSetTest('startingSequenceBlock', 'CurriculumInventorySequenceBlock');
    }
}
