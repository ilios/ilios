<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\SessionLearningMaterialInterface;

/**
 * Class SessionLearningMaterialManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class SessionLearningMaterialManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findSessionLearningMaterialBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findSessionLearningMaterialsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @deprecated
     */
    public function updateSessionLearningMaterial(
        SessionLearningMaterialInterface $sessionLearningMaterial,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($sessionLearningMaterial, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteSessionLearningMaterial(
        SessionLearningMaterialInterface $sessionLearningMaterial
    ) {
        $this->delete($sessionLearningMaterial);
    }

    /**
     * @deprecated
     */
    public function createSessionLearningMaterial()
    {
        return $this->create();
    }

    /**
     * @return int
     */
    public function getTotalSessionLearningMaterialCount()
    {
        return $this->em->createQuery('SELECT COUNT(l.id) FROM IliosCoreBundle:SessionLearningMaterial l')
            ->getSingleScalarResult();
    }
}
