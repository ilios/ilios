<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\LearningMaterialInterface;

/**
 * Interface LearningMaterialManagerInterface
 */
interface LearningMaterialManagerInterface
{
    /** 
     *@return LearningMaterialInterface
     */
    public function createLearningMaterial();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return LearningMaterialInterface
     */
    public function findLearningMaterialBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return LearningMaterialInterface[]|Collection
     */
    public function findLearningMaterialsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param LearningMaterialInterface $learningMaterial
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateLearningMaterial(LearningMaterialInterface $learningMaterial, $andFlush = true);

    /**
     * @param LearningMaterialInterface $learningMaterial
     *
     * @return void
     */
    public function deleteLearningMaterial(LearningMaterialInterface $learningMaterial);

    /**
     * @return string
     */
    public function getClass();
}
