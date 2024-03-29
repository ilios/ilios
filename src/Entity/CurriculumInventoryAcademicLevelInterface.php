<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\DescribableNullableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\NameableEntityInterface;
use Doctrine\Common\Collections\Collection;

interface CurriculumInventoryAcademicLevelInterface extends
    IdentifiableEntityInterface,
    NameableEntityInterface,
    DescribableNullableEntityInterface,
    LoggableEntityInterface
{
    public function setLevel(int $level): void;
    public function getLevel(): int;

    public function setReport(CurriculumInventoryReportInterface $report): void;
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
