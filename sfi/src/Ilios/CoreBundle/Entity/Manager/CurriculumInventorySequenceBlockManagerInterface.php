<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockInterface;

/**
 * Interface CurriculumInventorySequenceBlockManagerInterface
 * @package Ilios\CoreBundle\Manager
 */
interface CurriculumInventorySequenceBlockManagerInterface
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
     * @return CurriculumInventorySequenceBlockInterface[]|Collection
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
     *
     * @return void
     */
    public function updateCurriculumInventorySequenceBlock(
        CurriculumInventorySequenceBlockInterface $curriculumInventorySequenceBlock,
        $andFlush = true
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
     * @return string
     */
    public function getClass();

    /**
     * @return CurriculumInventorySequenceBlockInterface
     */
    public function createCurriculumInventorySequenceBlock();
}
