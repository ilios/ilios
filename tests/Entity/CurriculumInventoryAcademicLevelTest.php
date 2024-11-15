<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Entity\CurriculumInventoryAcademicLevel;

/**
 * Tests for Entity CurriculumInventoryAcademicLevel
 */
#[Group('model')]
#[CoversClass(CurriculumInventoryAcademicLevel::class)]
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

    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getStartingSequenceBlocks());
        $this->assertCount(0, $this->object->getEndingSequenceBlocks());
    }

    public function testSetLevel(): void
    {
        $this->basicSetTest('level', 'integer');
    }

    public function testSetName(): void
    {
        $this->basicSetTest('name', 'string');
    }

    public function testSetDescription(): void
    {
        $this->basicSetTest('description', 'string');
    }

    public function testSetReport(): void
    {
        $this->entitySetTest('report', 'CurriculumInventoryReport');
    }

    public function testAddStartingSequenceBlock(): void
    {
        $this->entityCollectionAddTest('startingSequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    public function testRemoveStartingSequenceBlock(): void
    {
        $this->entityCollectionRemoveTest('startingSequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    public function testGetStartingSequenceBlocks(): void
    {
        $this->entityCollectionSetTest('startingSequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    public function testAddEndingSequenceBlock(): void
    {
        $this->entityCollectionAddTest('endingSequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    public function testRemoveEndingSequenceBlock(): void
    {
        $this->entityCollectionRemoveTest('endingSequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    public function testGetEndingSequenceBlocks(): void
    {
        $this->entityCollectionSetTest('endingSequenceBlock', 'CurriculumInventorySequenceBlock');
    }

    protected function getObject(): CurriculumInventoryAcademicLevel
    {
        return $this->object;
    }
}
