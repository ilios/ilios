<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\SessionLearningMaterialInterface;

/**
 * Interface SessionLearningMaterialManagerInterface
 */
interface SessionLearningMaterialManagerInterface
{
    /** 
     *@return SessionLearningMaterialInterface
     */
    public function createSessionLearningMaterial();

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
     * @param int $limit
     * @param int $offset
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
}
