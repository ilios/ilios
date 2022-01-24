<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\DescribableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\NameableEntityInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Interface CurriculumInventoryAcademicLevelInterface
 */
interface CurriculumInventoryAcademicLevelInterface extends
    IdentifiableEntityInterface,
    NameableEntityInterface,
    DescribableEntityInterface,
    LoggableEntityInterface
{
    /**
     * @param int $level
     */
    public function setLevel($level);

    public function getLevel(): int;

    public function setReport(CurriculumInventoryReportInterface $report);

    public function getReport(): CurriculumInventoryReportInterface;

    public function setStartingSequenceBlocks(Collection $sequenceBlocks): void;

    public function addStartingSequenceBlock(
        CurriculumInventorySequenceBlockInterface $sequenceBlock
    ): void;

    public function removeStartingSequenceBlock(
        CurriculumInventorySequenceBlockInterface $sequenceBlock
    ): void;

    public function getStartingSequenceBlocks(): Collection;

    public function setEndingSequenceBlocks(Collection $sequenceBlocks): void;

    public function addEndingSequenceBlock(
        CurriculumInventorySequenceBlockInterface $sequenceBlock
    ): void;

    public function removeEndingSequenceBlock(
        CurriculumInventorySequenceBlockInterface $sequenceBlock
    ): void;

    public function getEndingSequenceBlocks(): Collection;
}
