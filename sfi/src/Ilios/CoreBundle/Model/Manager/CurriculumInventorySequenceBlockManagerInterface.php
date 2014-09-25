<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\CurriculumInventorySequenceBlockInterface;

/**
 * Interface CurriculumInventorySequenceBlockManagerInterface
 */
interface CurriculumInventorySequenceBlockManagerInterface
{
    /** 
     *@return CurriculumInventorySequenceBlockInterface
     */
    public function createCurriculumInventorySequenceBlock();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CurriculumInventorySequenceBlockInterface
     */
    public function findCurriculumInventorySequenceBlockBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return CurriculumInventorySequenceBlockInterface[]|Collection
     */
    public function findCurriculumInventorySequenceBlocksBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param CurriculumInventorySequenceBlockInterface $curriculumInventorySequenceBlock
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateCurriculumInventorySequenceBlock(CurriculumInventorySequenceBlockInterface $curriculumInventorySequenceBlock, $andFlush = true);

    /**
     * @param CurriculumInventorySequenceBlockInterface $curriculumInventorySequenceBlock
     *
     * @return void
     */
    public function deleteCurriculumInventorySequenceBlock(CurriculumInventorySequenceBlockInterface $curriculumInventorySequenceBlock);

    /**
     * @return string
     */
    public function getClass();
}
