<?php
namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockInterface;
use Mockery as m;

/**
 * Tests for Entity CurriculumInventorySequenceBlock
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
        $this->object->setReport(m::mock('Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface'));
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

        $this->object->setReport(m::mock('Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface'));
        $this->validate(0);
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getChildren());
        $this->assertEmpty($this->object->getSessions());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setRequired
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getRequired
     */
    public function testSetRequired()
    {
        $this->basicSetTest('required', 'integer');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setChildSequenceOrder
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getChildSequenceOrder
     */
    public function testSetChildSequenceOrder()
    {
        $this->basicSetTest('childSequenceOrder', 'integer');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setOrderInSequence
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getOrderInSequence
     */
    public function testSetOrderInSequence()
    {
        $this->basicSetTest('orderInSequence', 'integer');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setMinimum
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getMinimum
     */
    public function testSetMinimum()
    {
        $this->basicSetTest('minimum', 'integer');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setMaximum
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getMaximum
     */
    public function testSetMaximum()
    {
        $this->basicSetTest('maximum', 'integer');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setTrack
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::hasTrack
     */
    public function testSetTrack()
    {
        $this->booleanSetTest('track', false);
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setDescription
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setTitle
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setStartDate
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getStartDate
     */
    public function testSetStartDate()
    {
        $this->basicSetTest('startDate', 'datetime');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setEndDate
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getEndDate
     */
    public function testSetEndDate()
    {
        $this->basicSetTest('endDate', 'datetime');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setDuration
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getDuration
     */
    public function testSetDuration()
    {
        $this->basicSetTest('duration', 'integer');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setAcademicLevel
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getAcademicLevel
     */
    public function testSetAcademicLevel()
    {
        $this->entitySetTest('academicLevel', 'CurriculumInventoryAcademicLevel');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setCourse
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getCourse
     */
    public function testSetCourse()
    {
        $this->entitySetTest('course', 'Course');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setParent
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getParent
     */
    public function testSetParent()
    {
        $this->entitySetTest('parent', 'CurriculumInventorySequenceBlock');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setReport
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getReport
     */
    public function testSetReport()
    {
        $this->entitySetTest('report', 'CurriculumInventoryReport');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::compareSequenceBlocksWithOrderedStrategy
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
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::compareSequenceBlocksWithDefaultStrategy
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
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::addChild
     */
    public function testAddChild()
    {
        $this->entityCollectionAddTest('child', 'CurriculumInventorySequenceBlock', 'getChildren');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::removeChild
     */
    public function testRemoveChild()
    {
        $this->entityCollectionRemoveTest('child', 'CurriculumInventorySequenceBlock', 'getChildren');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getChildren
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::setChildren
     */
    public function testGetChildren()
    {
        $this->entityCollectionSetTest('child', 'CurriculumInventorySequenceBlock', 'getChildren', 'setChildren');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::addSession
     */
    public function testAddSession()
    {
        $this->entityCollectionAddTest('session', 'Session');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::removeSession
     */
    public function testRemoveSession()
    {
        $this->entityCollectionRemoveTest('session', 'Session');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock::getSessions
     */
    public function testGetSessions()
    {
        $this->entityCollectionSetTest('session', 'Session');
    }
}
