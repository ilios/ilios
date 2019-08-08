<?php

namespace App\Service\CurriculumInventory;

use App\Entity\CurriculumInventoryAcademicLevelInterface;
use App\Entity\CurriculumInventoryReportInterface;
use App\Entity\CurriculumInventorySequenceBlockInterface;
use App\Entity\CurriculumInventorySequenceInterface;
use App\Entity\Manager\CurriculumInventoryAcademicLevelManager;
use App\Entity\Manager\CurriculumInventoryReportManager;
use App\Entity\Manager\CurriculumInventorySequenceBlockManager;
use App\Entity\Manager\CurriculumInventorySequenceManager;
use DateTime;

/**
 * Service-class for rolling over a given curriculum inventory report.
 *
 * @category Service
 */
class ReportRollover
{
    /**
     * @var int
     */
    const START_DATE_DAY_OF_MONTH = 1;

    /**
     * @var int
     */
    const START_DATE_MONTH = 7;

    /**
     * @var int
     */
    const END_DATE_DAY_OF_MONTH = 30;

    /**
     * @var int
     */
    const END_DATE_MONTH = 6;

    /**
     * @var CurriculumInventoryReportManager $reportManager
     */
    protected $reportManager;

    /**
     * @var CurriculumInventoryAcademicLevelManager $academicLevelManager
     */
    protected $academicLevelManager;

    /**
     * @var CurriculumInventorySequenceManager $sequenceManager
     */
    protected $sequenceManager;

    /**
     * @var CurriculumInventorySequenceBlockManager $sequenceBlockManager
     */
    protected $sequenceBlockManager;

    /**
     * @param CurriculumInventoryReportManager $reportManager
     * @param CurriculumInventoryAcademicLevelManager $academicLevelManager
     * @param CurriculumInventorySequenceManager $sequenceManager
     * @param CurriculumInventorySequenceBlockManager $sequenceBlockManager
     */
    public function __construct(
        CurriculumInventoryReportManager $reportManager,
        CurriculumInventoryAcademicLevelManager $academicLevelManager,
        CurriculumInventorySequenceManager $sequenceManager,
        CurriculumInventorySequenceBlockManager $sequenceBlockManager
    ) {
        $this->reportManager = $reportManager;
        $this->academicLevelManager = $academicLevelManager;
        $this->sequenceManager = $sequenceManager;
        $this->sequenceBlockManager = $sequenceBlockManager;
    }

    /**
     * Rolls over (clones) a given curriculum inventory report and a subset of its associated data points.
     * @param CurriculumInventoryReportInterface $report The report to roll over.
     * @param string|null $newName Name override for the rolled-over report.
     * @param string|null $newDescription Description override for the rolled-over report.
     * @param int|null $newYear Academic year override for the rolled-over report.
     * @return CurriculumInventoryReportInterface The report created during rollover.
     */
    public function rollover(
        CurriculumInventoryReportInterface $report,
        $newName = null,
        $newDescription = null,
        $newYear = null
    ) {
        /* @var CurriculumInventoryReportInterface $newReport */
        $newReport = $this->reportManager->create();

        $newYear = $newYear ?: $report->getYear();

        $startDate = new DateTime();
        $startDate->setDate($newYear, self::START_DATE_MONTH, self::START_DATE_DAY_OF_MONTH);
        $endDate = new DateTime();
        $endDate->setDate($newYear + 1, self::END_DATE_MONTH, self::END_DATE_DAY_OF_MONTH);
        $newReport->setStartDate($startDate);
        $newReport->setEndDate($endDate);
        $newReport->setProgram($report->getProgram());
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

        $this->reportManager->update($newReport, false, false);

        $newLevels = [];
        $levels = $report->getAcademicLevels();
        foreach ($levels as $level) {
            /* @var CurriculumInventoryAcademicLevelInterface $newLevel */
            $newLevel = $this->academicLevelManager->create();
            $newLevel->setLevel($level->getLevel());
            $newLevel->setName($level->getName());
            $newLevel->setDescription($level->getDescription());
            $newReport->addAcademicLevel($newLevel);
            $newLevel->setReport($newReport);
            $this->academicLevelManager->update($newLevel, false, false);
            $newLevels[$newLevel->getLevel()] = $newLevel;
        }

        // recursively rollover sequence blocks.
        $topLevelBlocks = $report
            ->getSequenceBlocks()
            ->filter(function (CurriculumInventorySequenceBlockInterface $block) {
                return is_null($block->getParent());
            });

        foreach ($topLevelBlocks as $block) {
            $this->rolloverSequenceBlock($block, $newReport, $newLevels, null);
        }

        $sequence = $report->getSequence();
        /* @var  CurriculumInventorySequenceInterface $newSequence */
        $newSequence = $this->sequenceManager->create();
        $newSequence->setDescription($sequence->getDescription());
        $newReport->setSequence($newSequence);
        $newSequence->setReport($newReport);
        $this->sequenceManager->update($newSequence, true, false); // flush here.


        // generate token after the fact and persist report once more.
        $newReport->generateToken();
        $this->reportManager->update($newReport, true, true);

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
        CurriculumInventorySequenceBlockInterface $newParent = null
    ) {
        /* @var CurriculumInventorySequenceBlockInterface $newBlock */
        $newBlock = $this->sequenceBlockManager->create();
        $newBlock->setReport($newReport);
        $newBlock->setAcademicLevel($newLevels[$block->getAcademicLevel()->getLevel()]);
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
        $this->sequenceBlockManager->update($newBlock, false, false);

        foreach ($block->getChildren() as $child) {
            $this->rolloverSequenceBlock($child, $newReport, $newLevels, $newBlock);
        }
    }
}
