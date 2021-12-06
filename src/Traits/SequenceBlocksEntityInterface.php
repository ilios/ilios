<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\CurriculumInventorySequenceBlockInterface;

/**
 * Interface SequenceBlocksEntityInterface
 */
interface SequenceBlocksEntityInterface
{
    public function setSequenceBlocks(Collection $sequenceBlocks);

    public function addSequenceBlock(
        CurriculumInventorySequenceBlockInterface $sequenceBlock
    );

    public function removeSequenceBlock(
        CurriculumInventorySequenceBlockInterface $sequenceBlock
    );

    public function getSequenceBlocks(): Collection;
}
