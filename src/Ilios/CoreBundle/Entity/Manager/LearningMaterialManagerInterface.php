<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\LearningMaterialInterface;

/**
 * Interface LearningMaterialManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface LearningMaterialManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return LearningMaterialInterface
     */
    public function findLearningMaterialBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return LearningMaterialInterface[]
     */
    public function findLearningMaterialsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * Use a query term to find learning materials
     *
     * @param string $q
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return LearningMaterialInterface[]
     */
    public function findLearningMaterialsByQ(
        $q,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param LearningMaterialInterface $learningMaterial
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateLearningMaterial(
        LearningMaterialInterface $learningMaterial,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param LearningMaterialInterface $learningMaterial
     *
     * @return void
     */
    public function deleteLearningMaterial(
        LearningMaterialInterface $learningMaterial
    );

    /**
     * @return LearningMaterialInterface
     */
    public function createLearningMaterial();
    
    /**
     * Find all the File type learning materials
     * @param integer $limit
     * @param integer $offset
     *
     * @return LearningMaterialInterface[]
     */
    public function findFileLearningMaterials($limit, $offset);

    /**
     * @return integer
     */
    public function getTotalLearningMaterialCount();

    /**
     * @return integer
     */
    public function getTotalFileLearningMaterialCount();
}
