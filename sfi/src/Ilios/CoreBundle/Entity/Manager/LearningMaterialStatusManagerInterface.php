<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\LearningMaterialStatusInterface;

/**
 * Interface LearningMaterialStatusManagerInterface
 * @package Ilios\CoreBundle\Manager
 */
interface LearningMaterialStatusManagerInterface
{
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

    /**
     * @return LearningMaterialStatusInterface
     */
    public function createLearningMaterialStatus();
}