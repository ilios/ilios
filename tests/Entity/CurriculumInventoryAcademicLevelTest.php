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
    protected CurriculumInventoryAcademicLevel $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new CurriculumInventoryAcademicLevel();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'name',
            'level',
        ];
        $this->validateNotBlanks($notBlank);

        $this->object->setName('50 char max name test');
        $this->object->setLevel(4);
        $this->object->setDescription('');
        $this->validate(0);
        $this->object->setDescription('test');
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\CurriculumInventoryAcademicLevel::__construct
     */
    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getStartingSequenceBlocks());
        $this->assertCount(0, $this->object->getEndingSequenceBlocks());
    }

    /**
     * @covers \App\Entity\CurriculumInventoryAcademicLevel::setLevel
     * @covers \App\Entity\CurriculumInventoryAcademicLevel::getLevel
     */
    public function testSetLevel(): void
    {
        $this->basicSetTest('level', 'integer');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryAcademicLevel::setName
     * @covers \App\Entity\CurriculumInventoryAcademicLevel::getName
     */
    public function testSetName(): void
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryAcademicLevel::setDescription
     * @covers \App\Entity\CurriculumInventoryAcademicLevel::getDescription
     */
    public function testSetDescription(): void
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryAcademicLevel::setReport
     * @covers \App\Entity\CurriculumInventoryAcademicLevel::getReport
     */
    public function testSetReport(): void
    {
        $this->entitySetTest('report', 'CurriculumInventoryReport');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryAcademicLevel::addStartingSequenceBlock
     */
    public function testAddStartingSequenceBlock(): void
    {
        $this->entityCollectionAddTest('startingSequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryAcademicLevel::removeStartingSequenceBlock
     */
    public function testRemoveStartingSequenceBlock(): void
    {
        $this->entityCollectionRemoveTest('startingSequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryAcademicLevel::getStartingSequenceBlocks
     * @covers \App\Entity\CurriculumInventoryAcademicLevel::setStartingSequenceBlocks
     */
    public function testGetStartingSequenceBlocks(): void
    {
        $this->entityCollectionSetTest('startingSequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryAcademicLevel::addEndingSequenceBlock
     */
    public function testAddEndingSequenceBlock(): void
    {
        $this->entityCollectionAddTest('endingSequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryAcademicLevel::removeEndingSequenceBlock
     */
    public function testRemoveEndingSequenceBlock(): void
    {
        $this->entityCollectionRemoveTest('endingSequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryAcademicLevel::getEndingSequenceBlocks
     * @covers \App\Entity\CurriculumInventoryAcademicLevel::setEndingSequenceBlocks
     */
    public function testGetEndingSequenceBlocks(): void
    {
        $this->entityCollectionSetTest('endingSequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    protected function getObject(): CurriculumInventoryAcademicLevel
    {
        return $this->object;
    }
}
