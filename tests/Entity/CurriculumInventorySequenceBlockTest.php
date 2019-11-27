<?php
namespace App\Tests\Entity;

use App\Entity\CurriculumInventoryAcademicLevel;
use App\Entity\CurriculumInventorySequenceBlock;
use App\Entity\CurriculumInventorySequenceBlockInterface;
use Mockery as m;

/**
 * Tests for Entity CurriculumInventorySequenceBlock
 * @group model
 */
class CurriculumInventorySequenceBlockTest extends EntityBase
{
    /**
     * @var CurriculumInventorySequenceBlock
     */
    protected $object;

    /**
     * Instantiate a CurriculumInventorySequenceBlock object
     */
    protected function setUp()
    {
        $this->object = new CurriculumInventorySequenceBlock;
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'title',
            'childSequenceOrder',
            'orderInSequence',
            'minimum',
            'maximum',
            'duration'
        );
        $this->object->setReport(m::mock('App\Entity\CurriculumInventoryReportInterface'));
        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('test title for the block max 200');
        $this->object->setChildSequenceOrder(1);
        $this->object->setOrderInSequence(2);
        $this->object->setMinimum(1);
        $this->object->setMaximum(521);
        $this->object->setStartDate(new \DateTime());
        $this->object->setEndDate(new \DateTime());
        $this->object->setDuration(60);
        $this->validate(0);
    }

    public function testNotNullValidation()
    {
        $notNulls = array(
            'report'
        );
        $this->object->setTitle('test title for the block max 200');
        $this->object->setChildSequenceOrder(1);
        $this->object->setOrderInSequence(2);
        $this->object->setMinimum(1);
        $this->object->setMaximum(521);
        $this->object->setStartDate(new \DateTime());
        $this->object->setEndDate(new \DateTime());
        $this->object->setDuration(60);
        $this->validateNotNulls($notNulls);

        $this->object->setReport(m::mock('App\Entity\CurriculumInventoryReportInterface'));
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\CurriculumInventorySequenceBlock::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getChildren());
        $this->assertEmpty($this->object->getSessions());
    }

    /**
     * @covers \App\Entity\CurriculumInventorySequenceBlock::setRequired
     * @covers \App\Entity\CurriculumInventorySequenceBlock::getRequired
     */
    public function testSetRequired()
    {
        $this->basicSetTest('required', 'integer');
    }

    /**
     * @covers \App\Entity\CurriculumInventorySequenceBlock::setChildSequenceOrder
     * @covers \App\Entity\CurriculumInventorySequenceBlock::getChildSequenceOrder
     */
    public function testSetChildSequenceOrder()
    {
        $this->basicSetTest('childSequenceOrder', 'integer');
    }

    /**
     * @covers \App\Entity\CurriculumInventorySequenceBlock::setOrderInSequence
     * @covers \App\Entity\CurriculumInventorySequenceBlock::getOrderInSequence
     */
    public function testSetOrderInSequence()
    {
        $this->basicSetTest('orderInSequence', 'integer');
    }

    /**
     * @covers \App\Entity\CurriculumInventorySequenceBlock::setMinimum
     * @covers \App\Entity\CurriculumInventorySequenceBlock::getMinimum
     */
    public function testSetMinimum()
    {
        $this->basicSetTest('minimum', 'integer');
    }

    /**
     * @covers \App\Entity\CurriculumInventorySequenceBlock::setMaximum
     * @covers \App\Entity\CurriculumInventorySequenceBlock::getMaximum
     */
    public function testSetMaximum()
    {
        $this->basicSetTest('maximum', 'integer');
    }

    /**
     * @covers \App\Entity\CurriculumInventorySequenceBlock::setTrack
     * @covers \App\Entity\CurriculumInventorySequenceBlock::hasTrack
     */
    public function testSetTrack()
    {
        $this->booleanSetTest('track', false);
    }

    /**
     * @covers \App\Entity\CurriculumInventorySequenceBlock::setDescription
     * @covers \App\Entity\CurriculumInventorySequenceBlock::getDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers \App\Entity\CurriculumInventorySequenceBlock::setTitle
     * @covers \App\Entity\CurriculumInventorySequenceBlock::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\CurriculumInventorySequenceBlock::setStartDate
     * @covers \App\Entity\CurriculumInventorySequenceBlock::getStartDate
     */
    public function testSetStartDate()
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    /**
     * @covers \App\Entity\CurriculumInventorySequenceBlock::setEndDate
     * @covers \App\Entity\CurriculumInventorySequenceBlock::getEndDate
     */
    public function testSetEndDate()
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    /**
     * @covers \App\Entity\CurriculumInventorySequenceBlock::setDuration
     * @covers \App\Entity\CurriculumInventorySequenceBlock::getDuration
     */
    public function testSetDuration()
    {
        $this->basicSetTest('duration', 'integer');
    }

    /**
     * @covers \App\Entity\CurriculumInventorySequenceBlock::setAcademicLevel
     * @covers \App\Entity\CurriculumInventorySequenceBlock::getAcademicLevel
     */
    public function testSetAcademicLevel()
    {
        $this->entitySetTest('academicLevel', 'CurriculumInventoryAcademicLevel');
    }

    /**
     * @covers \App\Entity\CurriculumInventorySequenceBlock::setCourse
     * @covers \App\Entity\CurriculumInventorySequenceBlock::getCourse
     */
    public function testSetCourse()
    {
        $this->entitySetTest('course', 'Course');
    }

    /**
     * @covers \App\Entity\CurriculumInventorySequenceBlock::setParent
     * @covers \App\Entity\CurriculumInventorySequenceBlock::getParent
     */
    public function testSetParent()
    {
        $this->entitySetTest('parent', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \App\Entity\CurriculumInventorySequenceBlock::setReport
     * @covers \App\Entity\CurriculumInventorySequenceBlock::getReport
     */
    public function testSetReport()
    {
        $this->entitySetTest('report', 'CurriculumInventoryReport');
    }

    /**
     * @covers \App\Entity\CurriculumInventorySequenceBlock::compareSequenceBlocksWithOrderedStrategy
     * @dataProvider compareSequenceBlocksWithOrderedStrategyProvider
     *
     * @param CurriculumInventorySequenceBlockInterface $blockA
     * @param CurriculumInventorySequenceBlockInterface $blockB
     * @param int $expected
     */
    public function testCompareSequenceBlocksWithOrderedStrategy(
        CurriculumInventorySequenceBlockInterface $blockA,
        CurriculumInventorySequenceBlockInterface $blockB,
        $expected
    ) {
        $this->assertEquals(
            $expected,
            CurriculumInventorySequenceBlock::compareSequenceBlocksWithOrderedStrategy($blockA, $blockB)
        );
    }

    /**
     * @return array
     */
    public function compareSequenceBlocksWithOrderedStrategyProvider()
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

    /**
     * @covers \App\Entity\CurriculumInventorySequenceBlock::compareSequenceBlocksWithDefaultStrategy
     * @dataProvider compareSequenceBlocksWithDefaultStrategyProvider
     *
     * @param CurriculumInventorySequenceBlockInterface $blockA
     * @param CurriculumInventorySequenceBlockInterface $blockB
     * @param int $expected
     */
    public function testCompareSequenceBlocksWithDefaultStrategy(
        CurriculumInventorySequenceBlockInterface $blockA,
        CurriculumInventorySequenceBlockInterface $blockB,
        $expected
    ) {
        $this->assertEquals(
            $expected,
            CurriculumInventorySequenceBlock::compareSequenceBlocksWithDefaultStrategy($blockA, $blockB)
        );
    }

    /**
     * @return array
     */
    public function compareSequenceBlocksWithDefaultStrategyProvider()
    {
        $rhett = [];

        $level1 = new CurriculumInventoryAcademicLevel();
        $level1->setLevel(1);

        $level10 = new CurriculumInventoryAcademicLevel();
        $level10->setLevel(10);

        $blockA = new CurriculumInventorySequenceBlock();
        $blockA->setId(1);
        $blockA->setTitle("Alpha");
        $blockA->setStartDate(new \DateTime('2015-09-17'));
        $blockA->setAcademicLevel($level1);

        // same as A but with different level
        $blockB = new CurriculumInventorySequenceBlock();
        $blockB->setId(1);
        $blockB->setTitle("Alpha");
        $blockB->setStartDate(new \DateTime('2015-09-17'));
        $blockB->setAcademicLevel($level10);

        // same as A but with different start date
        $blockC = new CurriculumInventorySequenceBlock();
        $blockC->setId(1);
        $blockC->setTitle("Alpha");
        $blockC->setStartDate(new \DateTime('2019-09-17'));
        $blockC->setAcademicLevel($level1);

        // same as A but with different title
        $blockD = new CurriculumInventorySequenceBlock();
        $blockD->setId(1);
        $blockD->setTitle("Beta");
        $blockD->setStartDate(new \DateTime('2015-09-17'));
        $blockD->setAcademicLevel($level1);

        // same as A but with different id
        $blockE = new CurriculumInventorySequenceBlock();
        $blockE->setId(2);
        $blockE->setTitle("Alpha");
        $blockE->setStartDate(new \DateTime('2015-09-17'));
        $blockE->setAcademicLevel($level1);

        $rhett[] = [ $blockA, $blockA, 0 ];
        $rhett[] = [ $blockB, $blockA, 1 ];
        $rhett[] = [ $blockA, $blockB, -1 ];
        $rhett[] = [ $blockC, $blockA, 1 ];
        $rhett[] = [ $blockA, $blockC, -1 ];
        $rhett[] = [ $blockD, $blockA, 1 ];
        $rhett[] = [ $blockA, $blockD, -1 ];
        $rhett[] = [ $blockE, $blockA, 1 ];
        $rhett[] = [ $blockA, $blockE, -1 ];

        return $rhett;
    }

    /**
     * @covers \App\Entity\CurriculumInventorySequenceBlock::addChild
     */
    public function testAddChild()
    {
        $this->entityCollectionAddTest('child', 'CurriculumInventorySequenceBlock', 'getChildren');
    }

    /**
     * @covers \App\Entity\CurriculumInventorySequenceBlock::removeChild
     */
    public function testRemoveChild()
    {
        $this->entityCollectionRemoveTest('child', 'CurriculumInventorySequenceBlock', 'getChildren');
    }

    /**
     * @covers \App\Entity\CurriculumInventorySequenceBlock::getChildren
     * @covers \App\Entity\CurriculumInventorySequenceBlock::setChildren
     */
    public function testGetChildren()
    {
        $this->entityCollectionSetTest('child', 'CurriculumInventorySequenceBlock', 'getChildren', 'setChildren');
    }

    /**
     * @covers \App\Entity\CurriculumInventorySequenceBlock::addSession
     */
    public function testAddSession()
    {
        $this->entityCollectionAddTest('session', 'Session');
    }

    /**
     * @covers \App\Entity\CurriculumInventorySequenceBlock::removeSession
     */
    public function testRemoveSession()
    {
        $this->entityCollectionRemoveTest('session', 'Session');
    }

    /**
     * @covers \App\Entity\CurriculumInventorySequenceBlock::getSessions
     */
    public function testGetSessions()
    {
        $this->entityCollectionSetTest('session', 'Session');
    }

    /**
     * @covers \App\Entity\CurriculumInventorySequenceBlock::addExcludedSession
     */
    public function testAddExcludedSession()
    {
        $this->entityCollectionAddTest('excludedSession', 'Session');
    }

    /**
     * @covers \App\Entity\CurriculumInventorySequenceBlock::removeExcludedSession
     */
    public function testRemoveExcludedSession()
    {
        $this->entityCollectionRemoveTest('excludedSession', 'Session');
    }

    /**
     * @covers \App\Entity\CurriculumInventorySequenceBlock::getExcludedSessions
     */
    public function testGetExcludedSessions()
    {
        $this->entityCollectionSetTest('excludedSession', 'Session');
    }
}
