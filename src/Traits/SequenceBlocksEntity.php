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
    public function setSequenceBlocks(Collection $sequenceBlocks)
    {
        $this->sequenceBlocks = new ArrayCollection();

        foreach ($sequenceBlocks as $sequenceBlock) {
            $this->addSequenceBlock($sequenceBlock);
        }
    }

    public function addSequenceBlock(
        CurriculumInventorySequenceBlockInterface $sequenceBlock
    ) {
        if (!$this->sequenceBlocks->contains($sequenceBlock)) {
            $this->sequenceBlocks->add($sequenceBlock);
        }
    }

    public function removeSequenceBlock(
        CurriculumInventorySequenceBlockInterface $sequenceBlock
    ) {
        $this->sequenceBlocks->removeElement($sequenceBlock);
    }

    /**
    * @return CurriculumInventorySequenceBlockInterface[]|ArrayCollection
    */
    public function getSequenceBlocks()
    {
        return $this->sequenceBlocks;
    }
}
