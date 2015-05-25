<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\SessionLearningMaterialInterface;

/**
 * Interface SessionLearningMaterialManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface SessionLearningMaterialManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return SessionLearningMaterialInterface
     */
    public function findSessionLearningMaterialBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|SessionLearningMaterialInterface[]
     */
    public function findSessionLearningMaterialsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param SessionLearningMaterialInterface $sessionLearningMaterial
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateSessionLearningMaterial(
        SessionLearningMaterialInterface $sessionLearningMaterial,
        $andFlush = true,
        $forceId  = false
    );

    /**
     * @param SessionLearningMaterialInterface $sessionLearningMaterial
     *
     * @return void
     */
    public function deleteSessionLearningMaterial(
        SessionLearningMaterialInterface $sessionLearningMaterial
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return SessionLearningMaterialInterface
     */
    public function createSessionLearningMaterial();
}
