<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\Collection;
use App\Entity\CurriculumInventorySequenceBlockInterface;

/**
 * Interface SequenceBlocksEntityInterface
 */
interface SequenceBlocksEntityInterface
{
    public function setSequenceBlocks(Collection $sequenceBlocks): void;

    public function addSequenceBlock(
        CurriculumInventorySequenceBlockInterface $sequenceBlock
    ): void;

    public function removeSequenceBlock(
        CurriculumInventorySequenceBlockInterface $sequenceBlock
    ): void;

    public function getSequenceBlocks(): Collection;
}
