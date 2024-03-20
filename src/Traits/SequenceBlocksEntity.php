<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\CurriculumInventorySequenceBlockInterface;

/**
 * Class SequenceBlocksEntity
 */
trait SequenceBlocksEntity
{
    protected Collection $sequenceBlocks;

    public function setSequenceBlocks(Collection $sequenceBlocks): void
    {
        $this->sequenceBlocks = new ArrayCollection();

        foreach ($sequenceBlocks as $sequenceBlock) {
            $this->addSequenceBlock($sequenceBlock);
        }
    }

    public function addSequenceBlock(
        CurriculumInventorySequenceBlockInterface $sequenceBlock
    ): void {
        if (!$this->sequenceBlocks->contains($sequenceBlock)) {
            $this->sequenceBlocks->add($sequenceBlock);
        }
    }

    public function removeSequenceBlock(
        CurriculumInventorySequenceBlockInterface $sequenceBlock
    ): void {
        $this->sequenceBlocks->removeElement($sequenceBlock);
    }

    public function getSequenceBlocks(): Collection
    {
        return $this->sequenceBlocks;
    }
}
