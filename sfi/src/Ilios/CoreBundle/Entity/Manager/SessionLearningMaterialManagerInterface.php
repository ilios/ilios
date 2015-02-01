<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\SessionLearningMaterialInterface;

/**
 * Interface SessionLearningMaterialManagerInterface
 * @package Ilios\CoreBundle\Manager
 */
interface SessionLearningMaterialManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return SessionLearningMaterialInterface
     */
    public function findSessionLearningMaterialBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return SessionLearningMaterialInterface[]|Collection
     */
    public function findSessionLearningMaterialsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param SessionLearningMaterialInterface $sessionLearningMaterial
     * @param bool $andFlush
     *
     * @return void
     */
     public function updateSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial, $andFlush = true);

    /**
     * @param SessionLearningMaterialInterface $sessionLearningMaterial
     *
     * @return void
     */
    public function deleteSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial);

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return SessionLearningMaterialInterface
     */
    public function createSessionLearningMaterial();
}