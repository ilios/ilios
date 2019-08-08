<?php
namespace App\Tests\Service;

use App\Entity\CurriculumInventoryAcademicLevel;
use App\Entity\CurriculumInventoryReport;
use App\Entity\CurriculumInventorySequence;
use App\Entity\CurriculumInventorySequenceBlock;
use App\Entity\CurriculumInventorySequenceBlockInterface;
use App\Entity\Manager\CurriculumInventoryAcademicLevelManager;
use App\Entity\Manager\CurriculumInventoryReportManager;
use App\Entity\Manager\CurriculumInventorySequenceBlockManager;
use App\Entity\Manager\CurriculumInventorySequenceManager;
use App\Entity\User;
use App\Service\CurriculumInventory\ReportRollover;
use App\Tests\TestCase;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;

/**
 * Class ReportRolloverTest
 */
class ReportRolloverTest extends TestCase
{
    /**
     * @var m\MockInterface
     */
    protected $reportManager;

    /**
     * @var m\MockInterface
     */
    protected $academicLevelManager;

    /**
     * @var m\MockInterface
     */
    protected $sequenceManager;

    /**
     * @var m\MockInterface
     */
    protected $sequenceBlockManager;

    /**
     * @var ReportRollover
     */
    protected $service;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->reportManager = m::mock(CurriculumInventoryReportManager::class);
        $this->academicLevelManager = m::mock(CurriculumInventoryAcademicLevelManager::class);
        $this->sequenceManager = m::mock(CurriculumInventorySequenceManager::class);
        $this->sequenceBlockManager = m::mock(CurriculumInventorySequenceBlockManager::class);
        $this->service = new ReportRollover(
            $this->reportManager,
            $this->academicLevelManager,
            $this->sequenceManager,
            $this->sequenceBlockManager
        );

        $this->reportManager->shouldReceive('create')->andReturnUsing(function () {
            return new CurriculumInventoryReport();
        });
        $this->reportManager->shouldReceive('update');
        $this->sequenceManager->shouldReceive('create')->andReturnUsing(function () {
            return new CurriculumInventorySequence();
        });
        $this->sequenceManager->shouldReceive('update');
        $this->academicLevelManager->shouldReceive('create')->andReturnUsing(function () {
            return new CurriculumInventoryAcademicLevel();
        });
        $this->academicLevelManager->shouldReceive('update');
        $this->sequenceBlockManager->shouldReceive('create')->andReturnUsing(function () {
            return new CurriculumInventorySequenceBlock();
        });
        $this->sequenceBlockManager->shouldReceive('update');
    }

    /**
     * @inheritdoc
     */
    public function tearDown() : void
    {
        unset($this->service);
        unset($this->sequenceBlockManager);
        unset($this->sequenceManager);
        unset($this->academicLevelManager);
        unset($this->reportManager);
    }

    public function reportProvider()
    {
        $report = new CurriculumInventoryReport();
        $report->setStartDate(new DateTime('2019-08-07'));
        $report->setEndDate(new DateTime('2020-06-19'));
        $report->setYear(2019);
        $report->setName('My Report');
        $report->setDescription('Lorem Ipsum');
        $admin1 = new User();
        $admin2 = new User();
        $report->setAdministrators(new ArrayCollection([$admin1, $admin2]));
        $academicLevel1 = new CurriculumInventoryAcademicLevel();
        $academicLevel1->setLevel(1);
        $academicLevel1->setDescription('Level 1 description');
        $academicLevel1->setName('Level 1');
        $academicLevel2 = new CurriculumInventoryAcademicLevel();
        $academicLevel2->setLevel(2);
        $academicLevel2->setDescription('Level 2 description');
        $academicLevel2->setName('Level 2');
        $report->setAcademicLevels(new ArrayCollection([$academicLevel1, $academicLevel2]));
        $sequence = new CurriculumInventorySequence();
        $sequence->setDescription('this is a sequence');
        $report->setSequence($sequence);

        $topLevelBlock1 = new CurriculumInventorySequenceBlock();
        $topLevelBlock1->setDuration(10);
        $topLevelBlock1->setStartDate(new DateTime('01/01/2019'));
        $topLevelBlock1->setEndDate(new DateTime('01/02/2019'));
        $topLevelBlock1->setDescription('block description 1');
        $topLevelBlock1->setMinimum(1);
        $topLevelBlock1->setMaximum(50);
        $topLevelBlock1->setDuration(300);
        $topLevelBlock1->setChildSequenceOrder(CurriculumInventorySequenceBlockInterface::PARALLEL);
        $topLevelBlock1->setOrderInSequence(10);
        $topLevelBlock1->setRequired(CurriculumInventorySequenceBlockInterface::OPTIONAL);
        $topLevelBlock1->setTitle("Top Level Sequence Block 1");
        $topLevelBlock1->setTrack(true);
        $topLevelBlock1->setAcademicLevel($academicLevel1);
        $topLevelBlock2 = new CurriculumInventorySequenceBlock();
        $topLevelBlock2->setAcademicLevel($academicLevel2);
        $topLevelBlock2->setTitle("Top Level Sequence Block 2");
        $subLevelBlock1 = new CurriculumInventorySequenceBlock();
        $subLevelBlock1->setAcademicLevel($academicLevel1);
        $subLevelBlock1->setTitle("Sub Level Sequence Block 1");
        $subLevelBlock2 = new CurriculumInventorySequenceBlock();
        $subLevelBlock2->setAcademicLevel($academicLevel2);
        $subLevelBlock2->setTitle("Sub Level Sequence Block 2");
        $subSubLevelBlock1 = new CurriculumInventorySequenceBlock();
        $subSubLevelBlock1->setAcademicLevel($academicLevel2);
        $subSubLevelBlock1->setTitle("Sub-sub Level Sequence Block 1");
        $subLevelBlock1->setParent($topLevelBlock1);
        $subLevelBlock2->setParent($topLevelBlock1);
        $subSubLevelBlock1->setParent($subLevelBlock2);
        $topLevelBlock1->setChildren(new ArrayCollection([$subLevelBlock1, $subLevelBlock2]));
        $subLevelBlock2->setChildren(new ArrayCollection([$subSubLevelBlock1]));
        $report->setSequenceBlocks(
            new ArrayCollection([
                $topLevelBlock1,
                $topLevelBlock2,
                $subLevelBlock1, $subLevelBlock2,
                $subSubLevelBlock1
            ])
        );
        return [[ $report ]];
    }
    /**
     * @covers ReportRollover::rollover
     * @dataProvider reportProvider
     * @param CurriculumInventoryReport $report
     */
    public function testRollover(CurriculumInventoryReport $report)
    {
        $newReport = $this->service->rollover($report, 'new name', 'new description', 2022);

        $this->assertEquals('new name', $newReport->getName());
        $this->assertEquals('new description', $newReport->getDescription());
        $newStartDate = $newReport->getStartDate();
        $newEndDate = $newReport->getEndDate();
        $this->assertEquals('07/01/2022', $newStartDate->format('m/d/Y'));
        $this->assertEquals('06/30/2023', $newEndDate->format('m/d/Y'));
        $this->assertEquals(2022, $newReport->getYear());
        $this->assertEquals($report->getSequence()->getDescription(), $newReport->getSequence()->getDescription());

        $academicLevels = $report->getAcademicLevels()->toArray();
        $newAcademicLevels = $newReport->getAcademicLevels()->toArray();
        $this->assertEquals(count($academicLevels), count($newAcademicLevels));
        for ($i = 0, $n = count($academicLevels); $i < $n; $i++) {
            /* @var CurriculumInventoryAcademicLevel $academicLevel */
            $academicLevel = $academicLevels[$i];
            /* @var CurriculumInventoryAcademicLevel $newAcademicLevel */
            $newAcademicLevel = $newAcademicLevels[$i];
            $this->assertNotEquals($academicLevel, $newAcademicLevel);
            $this->assertEquals($academicLevel->getLevel(), $newAcademicLevel->getLevel());
            $this->assertEquals($academicLevel->getDescription(), $newAcademicLevel->getDescription());
            $this->assertEquals($academicLevel->getName(), $newAcademicLevel->getName());
        }
        $administrators = $report->getAdministrators()->toArray();
        $newAdministrators = $report->getAdministrators()->toArray();
        $this->assertEquals(count($administrators), count($newAdministrators));
        for ($i = 0, $n = count($administrators); $i < $n; $i++) {
            $this->assertEquals($administrators[$i], $newAdministrators[$i]);
        }

        $sequenceBlocks = $report->getSequenceBlocks()->toArray();
        $newSequenceBlocks = $newReport->getSequenceBlocks()->toArray();
        $this->assertEquals(count($sequenceBlocks), count($newSequenceBlocks));
        $topLevelFilterFn = function (CurriculumInventorySequenceBlock $block) {
            return !$block->getParent();
        };

        $topLevelBlocks = array_values(array_filter($sequenceBlocks, $topLevelFilterFn));
        $newTopLevelBlocks = array_values(array_filter($newSequenceBlocks, $topLevelFilterFn));
        $this->assertEquals(count($topLevelBlocks), count($newTopLevelBlocks));

        for ($i = 0, $n = count($topLevelBlocks); $i < $n; $i++) {
            $this->assertSequenceBlockEquals($topLevelBlocks[$i], $newTopLevelBlocks[$i]);
        }
    }

    /**
     * @covers ReportRollover::rollover
     * @dataProvider reportProvider
     * @param CurriculumInventoryReport $report
     */
    public function testRolloverKeepName($report)
    {
        $newReport = $this->service->rollover($report, null, 'new description', 2022);
        $this->assertEquals($report->getName(), $newReport->getName());
    }

    /**
     * @covers ReportRollover::rollover
     * @dataProvider reportProvider
     * @param CurriculumInventoryReport $report
     */
    public function testRolloverKeepDescription($report)
    {
        $newReport = $this->service->rollover($report, 'new name', null, 2022);
        $this->assertEquals($report->getDescription(), $newReport->getDescription());
    }

    /**
     * @covers ReportRollover::rollover
     * @dataProvider reportProvider
     * @param CurriculumInventoryReport $report
     */
    public function testRolloverKeepYear($report)
    {
        $newReport = $this->service->rollover($report, 'new name', 'new description', null);
        $year = $report->getYear();
        $followingYear = $year + 1;
        $this->assertEquals("07/01/${year}", $newReport->getStartDate()->format('m/d/Y'));
        $this->assertEquals("06/30/${followingYear}", $newReport->getEndDate()->format('m/d/Y'));
    }

    /**
     * @param CurriculumInventorySequenceBlock $sequenceBlock
     * @param CurriculumInventorySequenceBlock $newSequenceBlock
     */
    protected function assertSequenceBlockEquals(
        CurriculumInventorySequenceBlock $sequenceBlock,
        CurriculumInventorySequenceBlock $newSequenceBlock
    ) {
        $this->assertEquals(empty($sequenceBlock->getParent()), empty($newSequenceBlock->getParent()));
        if (! empty($sequenceBlock->getParent())) {
            $this->assertEquals($sequenceBlock->getParent()->getTitle(), $newSequenceBlock->getParent()->getTitle());
        }
        $children = $sequenceBlock->getChildren()->toArray();
        $newChildren = $newSequenceBlock->getChildren()->toArray();
        $this->assertEquals(count($children), count($newChildren));
        $this->assertNotEquals($sequenceBlock, $newSequenceBlock);
        $this->assertEquals($sequenceBlock->getTitle(), $newSequenceBlock->getTitle());
        $this->assertEquals($sequenceBlock->getDescription(), $newSequenceBlock->getDescription());
        $this->assertEquals($sequenceBlock->getStartDate(), $newSequenceBlock->getStartDate());
        $this->assertEquals($sequenceBlock->getEndDate(), $newSequenceBlock->getEndDate());
        $this->assertEquals($sequenceBlock->getRequired(), $newSequenceBlock->getRequired());
        $this->assertEquals($sequenceBlock->getMinimum(), $newSequenceBlock->getMinimum());
        $this->assertEquals($sequenceBlock->getMaximum(), $newSequenceBlock->getMaximum());
        $this->assertEquals($sequenceBlock->getDuration(), $newSequenceBlock->getDuration());
        $this->assertEquals($sequenceBlock->getOrderInSequence(), $newSequenceBlock->getOrderInSequence());
        $this->assertEquals($sequenceBlock->getChildSequenceOrder(), $newSequenceBlock->getChildSequenceOrder());
        $this->assertEquals($sequenceBlock->hasTrack(), $newSequenceBlock->hasTrack());
        ;
        $this->assertEquals(
            $sequenceBlock->getAcademicLevel()->getName(),
            $newSequenceBlock->getAcademicLevel()->getName()
        );

        if ($children) {
            for ($i = 0, $n = count($children); $i < $n; $i++) {
                $this->assertSequenceBlockEquals($children[$i], $newChildren[$i]);
            }
        }
    }
}
