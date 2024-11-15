<?php

declare(strict_types=1);

namespace App\Tests\Service\CurriculumInventory;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Entity\CurriculumInventoryAcademicLevel;
use App\Entity\CurriculumInventoryReport;
use App\Entity\CurriculumInventoryReportInterface;
use App\Entity\CurriculumInventorySequence;
use App\Entity\CurriculumInventorySequenceBlock;
use App\Entity\CurriculumInventorySequenceBlockInterface;
use App\Entity\Program;
use App\Entity\User;
use App\Repository\CurriculumInventoryAcademicLevelRepository;
use App\Repository\CurriculumInventoryReportRepository;
use App\Repository\CurriculumInventorySequenceBlockRepository;
use App\Repository\CurriculumInventorySequenceRepository;
use App\Service\CurriculumInventory\ReportRollover;
use App\Tests\TestCase;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery;
use Mockery\MockInterface;

use function count;

/**
 * Class ReportRolloverTest
 */
#[CoversClass(ReportRollover::class)]
class ReportRolloverTest extends TestCase
{
    protected MockInterface $reportRepository;
    protected MockInterface $academicLevelRepository;
    protected MockInterface $sequenceRepository;
    protected MockInterface $sequenceBlockRepository;
    protected ReportRollover $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->reportRepository = Mockery::mock(CurriculumInventoryReportRepository::class);
        $this->academicLevelRepository = Mockery::mock(CurriculumInventoryAcademicLevelRepository::class);
        $this->sequenceRepository = Mockery::mock(CurriculumInventorySequenceRepository::class);
        $this->sequenceBlockRepository = Mockery::mock(CurriculumInventorySequenceBlockRepository::class);
        $this->service = new ReportRollover(
            $this->reportRepository,
            $this->academicLevelRepository,
            $this->sequenceRepository,
            $this->sequenceBlockRepository
        );
        $this->reportRepository->shouldReceive('create')->andReturnUsing(function () {
            $report = new CurriculumInventoryReport();
            $report->setId(22);
            return $report;
        });
        $this->reportRepository->shouldReceive('update');
        $this->sequenceRepository->shouldReceive('create')->andReturnUsing(fn() => new CurriculumInventorySequence());
        $this->sequenceRepository->shouldReceive('update');
        $this->academicLevelRepository->shouldReceive('create')->andReturnUsing(
            fn() => new CurriculumInventoryAcademicLevel()
        );
        $this->academicLevelRepository->shouldReceive('update');
        $this->sequenceBlockRepository->shouldReceive('create')->andReturnUsing(
            fn() => new CurriculumInventorySequenceBlock()
        );
        $this->sequenceBlockRepository->shouldReceive('update');
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->service);
        unset($this->sequenceBlockRepository);
        unset($this->sequenceRepository);
        unset($this->academicLevelRepository);
        unset($this->reportRepository);
    }

    public static function reportProvider(): array
    {
        $report = new CurriculumInventoryReport();
        $report->setId(1);
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
        $academicLevel3 = new CurriculumInventoryAcademicLevel();
        $academicLevel3->setLevel(3);
        $academicLevel3->setDescription('Level 3 description');
        $academicLevel3->setName('Level 3');
        $report->setAcademicLevels(new ArrayCollection([$academicLevel1, $academicLevel2, $academicLevel3]));
        $sequence = new CurriculumInventorySequence();
        $sequence->setDescription('this is a sequence');
        $report->setSequence($sequence);
        $program = new Program();
        $report->setProgram($program);

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
        $topLevelBlock1->setStartingAcademicLevel($academicLevel1);
        $topLevelBlock1->setEndingAcademicLevel($academicLevel2);
        $topLevelBlock2 = new CurriculumInventorySequenceBlock();
        $topLevelBlock2->setStartingAcademicLevel($academicLevel2);
        $topLevelBlock2->setEndingAcademicLevel($academicLevel3);
        $topLevelBlock2->setTitle("Top Level Sequence Block 2");
        $subLevelBlock1 = new CurriculumInventorySequenceBlock();
        $subLevelBlock1->setStartingAcademicLevel($academicLevel1);
        $subLevelBlock1->setEndingAcademicLevel($academicLevel2);
        $subLevelBlock1->setTitle("Sub Level Sequence Block 1");
        $subLevelBlock2 = new CurriculumInventorySequenceBlock();
        $subLevelBlock2->setStartingAcademicLevel($academicLevel2);
        $subLevelBlock2->setEndingAcademicLevel($academicLevel3);
        $subLevelBlock2->setTitle("Sub Level Sequence Block 2");
        $subSubLevelBlock1 = new CurriculumInventorySequenceBlock();
        $subSubLevelBlock1->setStartingAcademicLevel($academicLevel2);
        $subSubLevelBlock1->setEndingAcademicLevel($academicLevel3);
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
                $subSubLevelBlock1,
            ])
        );
        return [[ $report ]];
    }
    #[DataProvider('reportProvider')]
    public function testRollover(CurriculumInventoryReport $report): void
    {
        $newReport = $this->service->rollover($report, $report->getProgram(), 'new name', 'new description', 2022);
        $this->assertEquals('new name', $newReport->getName());
        $this->assertEquals('new description', $newReport->getDescription());
        $newStartDate = $newReport->getStartDate();
        $newEndDate = $newReport->getEndDate();
        $this->assertEquals('07/01/2022', $newStartDate->format('m/d/Y'));
        $this->assertEquals('06/30/2023', $newEndDate->format('m/d/Y'));
        $this->assertEquals(2022, $newReport->getYear());
        $this->assertEquals($report->getSequence()->getDescription(), $newReport->getSequence()->getDescription());
        $this->assertEquals($report->getProgram(), $newReport->getProgram());
        $academicLevels = $report->getAcademicLevels()->toArray();
        $newAcademicLevels = $newReport->getAcademicLevels()->toArray();
        $this->assertEquals(count($academicLevels), count($newAcademicLevels));
        for ($i = 0, $n = count($academicLevels); $i < $n; $i++) {
            /** @var CurriculumInventoryAcademicLevel $academicLevel */
            $academicLevel = $academicLevels[$i];
            /** @var CurriculumInventoryAcademicLevel $newAcademicLevel */
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
        $topLevelFilterFn = fn(CurriculumInventorySequenceBlock $block) => !$block->getParent();

        $topLevelBlocks = array_values(array_filter($sequenceBlocks, $topLevelFilterFn));
        $newTopLevelBlocks = array_values(array_filter($newSequenceBlocks, $topLevelFilterFn));
        $this->assertEquals(count($topLevelBlocks), count($newTopLevelBlocks));

        for ($i = 0, $n = count($topLevelBlocks); $i < $n; $i++) {
            $this->assertSequenceBlockEquals($topLevelBlocks[$i], $newTopLevelBlocks[$i]);
        }
    }

    #[DataProvider('reportProvider')]
    public function testRolloverKeepName(CurriculumInventoryReportInterface $report): void
    {
        $newReport = $this->service->rollover($report, $report->getProgram(), null, 'new description', 2022);
        $this->assertEquals($report->getName(), $newReport->getName());
    }

    #[DataProvider('reportProvider')]
    public function testRolloverKeepDescription(CurriculumInventoryReportInterface $report): void
    {
        $newReport = $this->service->rollover($report, $report->getProgram(), 'new name', null, 2022);
        $this->assertEquals($report->getDescription(), $newReport->getDescription());
    }

    #[DataProvider('reportProvider')]
    public function testRolloverKeepYear(CurriculumInventoryReportInterface $report): void
    {
        $newReport = $this->service->rollover($report, $report->getProgram(), 'new name', 'new description', null);
        $year = $report->getYear();
        $followingYear = $year + 1;
        $this->assertEquals("07/01/{$year}", $newReport->getStartDate()->format('m/d/Y'));
        $this->assertEquals("06/30/{$followingYear}", $newReport->getEndDate()->format('m/d/Y'));
    }

    protected function assertSequenceBlockEquals(
        CurriculumInventorySequenceBlockInterface $sequenceBlock,
        CurriculumInventorySequenceBlockInterface $newSequenceBlock
    ): void {
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
            $sequenceBlock->getStartingAcademicLevel()->getName(),
            $newSequenceBlock->getStartingAcademicLevel()->getName()
        );
        $this->assertEquals(
            $sequenceBlock->getEndingAcademicLevel()->getName(),
            $newSequenceBlock->getEndingAcademicLevel()->getName()
        );

        if ($children) {
            for ($i = 0, $n = count($children); $i < $n; $i++) {
                $this->assertSequenceBlockEquals($children[$i], $newChildren[$i]);
            }
        }
    }

    #[DataProvider('reportProvider')]
    public function testRolloverWithDifferentProgram(CurriculumInventoryReportInterface $report): void
    {
        $program = new Program();
        $program->setTitle('something else');
        $newReport = $this->service->rollover($report, $program);
        $this->assertNotEquals($report->getProgram(), $newReport->getProgram());
        $this->assertEquals($program, $newReport->getProgram());
    }
}
