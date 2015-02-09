<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\LearningMaterialInterface;

/**
 * Interface LearningMaterialManagerInterface
 * @package Ilios\CoreBundle\Manager
 */
interface LearningMaterialManagerInterface
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
     * @return LearningMaterialInterface[]|Collection
     */
    public function findLearningMaterialsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param LearningMaterialInterface $learningMaterial
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateLearningMaterial(
        LearningMaterialInterface $learningMaterial,
        $andFlush = true
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
     * @return string
     */
    public function getClass();

    /**
     * @return LearningMaterialInterface
     */
    public function createLearningMaterial();
}
