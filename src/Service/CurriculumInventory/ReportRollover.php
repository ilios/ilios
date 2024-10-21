<?php

declare(strict_types=1);

namespace App\Service\CurriculumInventory;

use App\Entity\CurriculumInventoryAcademicLevelInterface;
use App\Entity\CurriculumInventoryReportInterface;
use App\Entity\CurriculumInventorySequenceBlockInterface;
use App\Entity\CurriculumInventorySequenceInterface;
use App\Entity\ProgramInterface;
use App\Repository\CurriculumInventoryAcademicLevelRepository;
use App\Repository\CurriculumInventoryReportRepository;
use App\Repository\CurriculumInventorySequenceBlockRepository;
use App\Repository\CurriculumInventorySequenceRepository;
use DateTime;

/**
 * Service-class for rolling over a given curriculum inventory report.
 *
 * @category Service
 */
class ReportRollover
{
    private const int START_DATE_DAY_OF_MONTH = 1;
    private const int START_DATE_MONTH = 7;
    private const int END_DATE_DAY_OF_MONTH = 30;
    private const int END_DATE_MONTH = 6;

    public function __construct(
        protected CurriculumInventoryReportRepository $reportRepository,
        protected CurriculumInventoryAcademicLevelRepository $academicLevelRepository,
        protected CurriculumInventorySequenceRepository $sequenceRepository,
        protected CurriculumInventorySequenceBlockRepository $sequenceBlockRepository
    ) {
    }

    /**
     * Rolls over (clones) a given curriculum inventory report and a subset of its associated data points.
     * @param CurriculumInventoryReportInterface $report The report to roll over.
     * @param ProgramInterface $program The program to set on the rolled-over report.
     * @param string|null $newName Name override for the rolled-over report.
     * @param string|null $newDescription Description override for the rolled-over report.
     * @param int|null $newYear Academic year override for the rolled-over report.
     */
    public function rollover(
        CurriculumInventoryReportInterface $report,
        ProgramInterface $program,
        ?string $newName = null,
        ?string $newDescription = null,
        ?int $newYear = null
    ): CurriculumInventoryReportInterface {
        /** @var CurriculumInventoryReportInterface $newReport */
        $newReport = $this->reportRepository->create();

        $newYear = $newYear ?: $report->getYear();

        $startDate = new DateTime();
        $startDate->setDate($newYear, self::START_DATE_MONTH, self::START_DATE_DAY_OF_MONTH);
        $endDate = new DateTime();
        $endDate->setDate($newYear + 1, self::END_DATE_MONTH, self::END_DATE_DAY_OF_MONTH);
        $newReport->setStartDate($startDate);
        $newReport->setEndDate($endDate);
        $newReport->setProgram($program);
        $newReport->setAdministrators($report->getAdministrators());
        if (isset($newName)) {
            $newReport->setName($newName);
        } else {
            $newReport->setName($report->getName());
        }
        if (isset($newDescription)) {
            $newReport->setDescription($newDescription);
        } else {
            $newReport->setDescription($report->getDescription());
        }
        $newReport->setYear($newYear);

        $this->reportRepository->update($newReport, false, false);

        $newLevels = [];
        $levels = $report->getAcademicLevels();
        foreach ($levels as $level) {
            /** @var CurriculumInventoryAcademicLevelInterface $newLevel */
            $newLevel = $this->academicLevelRepository->create();
            $newLevel->setLevel($level->getLevel());
            $newLevel->setName($level->getName());
            $newLevel->setDescription($level->getDescription());
            $newReport->addAcademicLevel($newLevel);
            $newLevel->setReport($newReport);
            $this->academicLevelRepository->update($newLevel, false, false);
            $newLevels[$newLevel->getLevel()] = $newLevel;
        }

        // recursively rollover sequence blocks.
        $topLevelBlocks = $report
            ->getSequenceBlocks()
            ->filter(fn(CurriculumInventorySequenceBlockInterface $block) => is_null($block->getParent()));

        foreach ($topLevelBlocks as $block) {
            $this->rolloverSequenceBlock($block, $newReport, $newLevels, null);
        }

        $sequence = $report->getSequence();
        /** @var  CurriculumInventorySequenceInterface $newSequence */
        $newSequence = $this->sequenceRepository->create();
        $newSequence->setDescription($sequence->getDescription());
        $newReport->setSequence($newSequence);
        $newSequence->setReport($newReport);
        $this->sequenceRepository->update($newSequence, true, false); // flush here.


        // generate token after the fact and persist report once more.
        $newReport->generateToken();
        $this->reportRepository->update($newReport, true, true);

        return $newReport;
    }

    /**
     * Recursively copies nested sequence blocks for rollover.
     *
     * @param CurriculumInventorySequenceBlockInterface $block The block to copy.
     * @param CurriculumInventoryReportInterface $newReport The new report to roll over into.
     * @param CurriculumInventoryAcademicLevelInterface[] $newLevels A map of new academic levels, indexed by level.
     * @param CurriculumInventorySequenceBlockInterface|null $newParent The new parent block for this copy.
     */
    protected function rolloverSequenceBlock(
        CurriculumInventorySequenceBlockInterface $block,
        CurriculumInventoryReportInterface $newReport,
        array $newLevels,
        ?CurriculumInventorySequenceBlockInterface $newParent = null
    ): void {
        /** @var CurriculumInventorySequenceBlockInterface $newBlock */
        $newBlock = $this->sequenceBlockRepository->create();
        $newBlock->setReport($newReport);
        $newBlock->setStartingAcademicLevel($newLevels[$block->getStartingAcademicLevel()->getLevel()]);
        $newBlock->setEndingAcademicLevel($newLevels[$block->getEndingAcademicLevel()->getLevel()]);
        $newBlock->setDescription($block->getDescription());
        $newBlock->setEndDate($block->getEndDate());
        $newBlock->setStartDate($block->getStartDate());
        $newBlock->setChildSequenceOrder($block->getChildSequenceOrder());
        $newBlock->setDuration($block->getDuration());
        $newBlock->setTitle($block->getTitle());
        $newBlock->setOrderInSequence($block->getOrderInSequence());
        $newBlock->setMinimum($block->getMinimum());
        $newBlock->setMaximum($block->getMaximum());
        $newBlock->setTrack($block->hasTrack());
        $newBlock->setRequired($block->getRequired());
        if ($newParent) {
            $newBlock->setParent($newParent);
            $newParent->addChild($newBlock);
        }

        $newReport->addSequenceBlock($newBlock);
        $this->sequenceBlockRepository->update($newBlock, false, false);

        foreach ($block->getChildren() as $child) {
            $this->rolloverSequenceBlock($child, $newReport, $newLevels, $newBlock);
        }
    }
}
