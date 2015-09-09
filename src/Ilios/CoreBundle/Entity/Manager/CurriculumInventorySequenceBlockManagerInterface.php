<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockInterface;

/**
 * Interface CurriculumInventorySequenceBlockManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface CurriculumInventorySequenceBlockManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CurriculumInventorySequenceBlockInterface
     */
    public function findCurriculumInventorySequenceBlockBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|CurriculumInventorySequenceBlockInterface[]
     */
    public function findCurriculumInventorySequenceBlocksBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param CurriculumInventorySequenceBlockInterface $curriculumInventorySequenceBlock
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateCurriculumInventorySequenceBlock(
        CurriculumInventorySequenceBlockInterface $curriculumInventorySequenceBlock,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param CurriculumInventorySequenceBlockInterface $curriculumInventorySequenceBlock
     *
     * @return void
     */
    public function deleteCurriculumInventorySequenceBlock(
        CurriculumInventorySequenceBlockInterface $curriculumInventorySequenceBlock
    );

    /**
     * @return CurriculumInventorySequenceBlockInterface
     */
    public function createCurriculumInventorySequenceBlock();

    /**
     * @todo
     */
    public function getBlocks($reportId);
}
