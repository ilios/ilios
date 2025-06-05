<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Entity\CurriculumInventoryAcademicLevel;
use App\Entity\CurriculumInventoryReportInterface;
use App\Entity\CurriculumInventorySequenceBlock;
use App\Entity\CurriculumInventorySequenceBlockInterface;
use DateTime;
use Mockery as m;

/**
 * Tests for Entity CurriculumInventorySequenceBlock
 */
#[Group('model')]
#[CoversClass(CurriculumInventorySequenceBlock::class)]
final class CurriculumInventorySequenceBlockTest extends EntityBase
{
    protected CurriculumInventorySequenceBlock $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new CurriculumInventorySequenceBlock();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'title',
        ];
        $this->object->setReport(m::mock(CurriculumInventoryReportInterface::class));
        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('test title for the block max 200');
        $this->object->setChildSequenceOrder(1);
        $this->object->setOrderInSequence(2);
        $this->object->setMinimum(1);
        $this->object->setMaximum(521);
        $this->object->setStartDate(new DateTime());
        $this->object->setEndDate(new DateTime());
        $this->object->setDescription('');
        $this->validate(0);
        $this->object->setDescription('test');
        $this->validate(0);
    }

    public function testNotNullValidation(): void
    {
        $notNulls = [
            'report',
        ];
        $this->object->setTitle('test title for the block max 200');
        $this->object->setChildSequenceOrder(1);
        $this->object->setOrderInSequence(2);
        $this->object->setMinimum(1);
        $this->object->setMaximum(521);
        $this->object->setStartDate(new DateTime());
        $this->object->setEndDate(new DateTime());
        $this->object->setDuration(60);
        $this->validateNotNulls($notNulls);

        $this->object->setReport(m::mock(CurriculumInventoryReportInterface::class));
        $this->validate(0);
    }

    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getChildren());
        $this->assertCount(0, $this->object->getSessions());
    }

    public function testSetRequired(): void
    {
        $this->basicSetTest('required', 'integer');
    }

    public function testSetChildSequenceOrder(): void
    {
        $this->basicSetTest('childSequenceOrder', 'integer');
    }

    public function testSetOrderInSequence(): void
    {
        $this->basicSetTest('orderInSequence', 'integer');
    }

    public function testSetMinimum(): void
    {
        $this->basicSetTest('minimum', 'integer');
    }

    public function testSetMaximum(): void
    {
        $this->basicSetTest('maximum', 'integer');
    }

    public function testSetTrack(): void
    {
        $this->booleanSetTest('track', false);
    }

    public function testSetDescription(): void
    {
        $this->basicSetTest('description', 'string');
    }

    public function testSetTitle(): void
    {
        $this->basicSetTest('title', 'string');
    }

    public function testSetStartDate(): void
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    public function testSetEndDate(): void
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    public function testSetDuration(): void
    {
        $this->basicSetTest('duration', 'integer');
    }

    public function testSetStartingAcademicLevel(): void
    {
        $this->entitySetTest('startingAcademicLevel', 'CurriculumInventoryAcademicLevel');
    }

    public function testSetEndingAcademicLevel(): void
    {
        $this->entitySetTest('endingAcademicLevel', 'CurriculumInventoryAcademicLevel');
    }

    public function testSetCourse(): void
    {
        $this->entitySetTest('course', 'Course');
    }

    public function testSetParent(): void
    {
        $this->entitySetTest('parent', 'CurriculumInventorySequenceBlock');
    }

    public function testSetReport(): void
    {
        $this->entitySetTest('report', 'CurriculumInventoryReport');
    }


    #[DataProvider('compareSequenceBlocksWithOrderedStrategyProvider')]
    public function testCompareSequenceBlocksWithOrderedStrategy(
        CurriculumInventorySequenceBlockInterface $blockA,
        CurriculumInventorySequenceBlockInterface $blockB,
        int $expected
    ): void {
        $this->assertEquals(
            $expected,
            CurriculumInventorySequenceBlock::compareSequenceBlocksWithOrderedStrategy($blockA, $blockB)
        );
    }

    public static function compareSequenceBlocksWithOrderedStrategyProvider(): array
    {
        $rhett = [];

        $blockA = new CurriculumInventorySequenceBlock();
        $blockA->setId(1);
        $blockA->setOrderInSequence(1);

        // same as A but different order
        $blockB = new CurriculumInventorySequenceBlock();
        $blockB->setId(1);
        $blockB->setOrderInSequence(2);

        // same as B but different id
        $blockC = new CurriculumInventorySequenceBlock();
        $blockC->setId(2);
        $blockC->setOrderInSequence(2);

        $rhett[] = [ $blockA, $blockA, 0 ];
        $rhett[] = [ $blockA, $blockB, -1 ];
        $rhett[] = [ $blockB, $blockA, 1 ];
        $rhett[] = [ $blockB, $blockC, 0 ];
        $rhett[] = [ $blockC, $blockB, 0 ];
        $rhett[] = [ $blockA, $blockC, -1 ];
        $rhett[] = [ $blockC, $blockA, 1 ];

        return $rhett;
    }


    #[DataProvider('compareSequenceBlocksWithDefaultStrategyProvider')]
    public function testCompareSequenceBlocksWithDefaultStrategy(
        CurriculumInventorySequenceBlockInterface $blockA,
        CurriculumInventorySequenceBlockInterface $blockB,
        int $expected
    ): void {
        $this->assertEquals(
            $expected,
            CurriculumInventorySequenceBlock::compareSequenceBlocksWithDefaultStrategy($blockA, $blockB)
        );
    }

    public static function compareSequenceBlocksWithDefaultStrategyProvider(): array
    {
        $rhett = [];

        $level1 = new CurriculumInventoryAcademicLevel();
        $level1->setLevel(1);

        $level2 = new CurriculumInventoryAcademicLevel();
        $level2->setLevel(2);

        $level10 = new CurriculumInventoryAcademicLevel();
        $level10->setLevel(10);

        $blockA = new CurriculumInventorySequenceBlock();
        $blockA->setId(1);
        $blockA->setTitle("Alpha");
        $blockA->setStartDate(new DateTime('2015-09-17'));
        $blockA->setStartingAcademicLevel($level1);
        $blockA->setEndingAcademicLevel($level1);

        // same as A but with different level
        $blockB = new CurriculumInventorySequenceBlock();
        $blockB->setId(1);
        $blockB->setTitle("Alpha");
        $blockB->setStartDate(new DateTime('2015-09-17'));
        $blockB->setStartingAcademicLevel($level10);
        $blockB->setEndingAcademicLevel($level1);

        // same as A but with different start date
        $blockC = new CurriculumInventorySequenceBlock();
        $blockC->setId(1);
        $blockC->setTitle("Alpha");
        $blockC->setStartDate(new DateTime('2019-09-17'));
        $blockC->setStartingAcademicLevel($level1);
        $blockC->setEndingAcademicLevel($level1);

        // same as A but with different end level
        $blockD = new CurriculumInventorySequenceBlock();
        $blockD->setId(1);
        $blockD->setTitle("Alpha");
        $blockD->setStartDate(new DateTime('2015-09-17'));
        $blockD->setStartingAcademicLevel($level1);
        $blockD->setEndingAcademicLevel($level2);

        // same as A but with different title
        $blockE = new CurriculumInventorySequenceBlock();
        $blockE->setId(1);
        $blockE->setTitle("Beta");
        $blockE->setStartDate(new DateTime('2015-09-17'));
        $blockE->setStartingAcademicLevel($level1);
        $blockE->setEndingAcademicLevel($level1);


        // same as A but with different id
        $blockF = new CurriculumInventorySequenceBlock();
        $blockF->setId(2);
        $blockF->setTitle("Alpha");
        $blockF->setStartDate(new DateTime('2015-09-17'));
        $blockF->setStartingAcademicLevel($level1);
        $blockF->setEndingAcademicLevel($level1);

        $rhett[] = [ $blockA, $blockA, 0 ];
        $rhett[] = [ $blockB, $blockA, 1 ];
        $rhett[] = [ $blockA, $blockB, -1 ];
        $rhett[] = [ $blockC, $blockA, 1 ];
        $rhett[] = [ $blockA, $blockC, -1 ];
        $rhett[] = [ $blockD, $blockA, 1 ];
        $rhett[] = [ $blockA, $blockD, -1 ];
        $rhett[] = [ $blockE, $blockA, 1 ];
        $rhett[] = [ $blockA, $blockE, -1 ];
        $rhett[] = [ $blockF, $blockA, 1 ];
        $rhett[] = [ $blockA, $blockF, -1 ];
        return $rhett;
    }

    public function testAddChild(): void
    {
        $this->entityCollectionAddTest('child', 'CurriculumInventorySequenceBlock', 'getChildren');
    }

    public function testRemoveChild(): void
    {
        $this->entityCollectionRemoveTest('child', 'CurriculumInventorySequenceBlock', 'getChildren');
    }

    public function testGetChildren(): void
    {
        $this->entityCollectionSetTest('child', 'CurriculumInventorySequenceBlock', 'getChildren', 'setChildren');
    }

    public function testAddSession(): void
    {
        $this->entityCollectionAddTest('session', 'Session');
    }

    public function testRemoveSession(): void
    {
        $this->entityCollectionRemoveTest('session', 'Session');
    }

    public function testGetSessions(): void
    {
        $this->entityCollectionSetTest('session', 'Session');
    }

    public function testAddExcludedSession(): void
    {
        $this->entityCollectionAddTest('excludedSession', 'Session');
    }

    public function testRemoveExcludedSession(): void
    {
        $this->entityCollectionRemoveTest('excludedSession', 'Session');
    }

    public function testGetExcludedSessions(): void
    {
        $this->entityCollectionSetTest('excludedSession', 'Session');
    }

    protected function getObject(): CurriculumInventorySequenceBlock
    {
        return $this->object;
    }
}
