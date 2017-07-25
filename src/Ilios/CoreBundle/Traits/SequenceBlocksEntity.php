<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockInterface;

/**
 * Class SequenceBlocksEntity
 */
trait SequenceBlocksEntity
{
    /**
     * @param Collection $sequenceBlocks
     */
    public function setSequenceBlocks(Collection $sequenceBlocks)
    {
        $this->sequenceBlocks = new ArrayCollection();

        foreach ($sequenceBlocks as $sequenceBlock) {
            $this->addSequenceBlock($sequenceBlock);
        }
    }

    /**
     * @param CurriculumInventorySequenceBlockInterface $sequenceBlock
     */
    public function addSequenceBlock(
        CurriculumInventorySequenceBlockInterface $sequenceBlock
    ) {
        if (!$this->sequenceBlocks->contains($sequenceBlock)) {
            $this->sequenceBlocks->add($sequenceBlock);
        }
    }

    /**
     * @param CurriculumInventorySequenceBlockInterface $sequenceBlock
     */
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
