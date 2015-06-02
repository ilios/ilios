<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\LearningMaterialStatusInterface;

/**
 * Interface LearningMaterialStatusManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface LearningMaterialStatusManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return LearningMaterialStatusInterface
     */
    public function findLearningMaterialStatusBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|LearningMaterialStatusInterface[]
     */
    public function findLearningMaterialStatusesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param LearningMaterialStatusInterface $learningMaterialStatus
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateLearningMaterialStatus(
        LearningMaterialStatusInterface $learningMaterialStatus,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param LearningMaterialStatusInterface $learningMaterialStatus
     *
     * @return void
     */
    public function deleteLearningMaterialStatus(
        LearningMaterialStatusInterface $learningMaterialStatus
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return LearningMaterialStatusInterface
     */
    public function createLearningMaterialStatus();
}
