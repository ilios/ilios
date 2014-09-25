<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\LearningMaterialStatusInterface;

/**
 * Interface LearningMaterialStatusManagerInterface
 */
interface LearningMaterialStatusManagerInterface
{
    /** 
     *@return LearningMaterialStatusInterface
     */
    public function createLearningMaterialStatus();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return LearningMaterialStatusInterface
     */
    public function findLearningMaterialStatusBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return LearningMaterialStatusInterface[]|Collection
     */
    public function findLearningMaterialStatusesBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param LearningMaterialStatusInterface $learningMaterialStatus
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateLearningMaterialStatus(LearningMaterialStatusInterface $learningMaterialStatus, $andFlush = true);

    /**
     * @param LearningMaterialStatusInterface $learningMaterialStatus
     *
     * @return void
     */
    public function deleteLearningMaterialStatus(LearningMaterialStatusInterface $learningMaterialStatus);

    /**
     * @return string
     */
    public function getClass();
}
