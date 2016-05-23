<?php

namespace Ilios\CoreBundle\Entity\Manager;

/**
 * Class SessionLearningMaterialManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class SessionLearningMaterialManager extends BaseManager
{
    /**
     * @return int
     */
    public function getTotalSessionLearningMaterialCount()
    {
        return $this->em->createQuery('SELECT COUNT(l.id) FROM IliosCoreBundle:SessionLearningMaterial l')
            ->getSingleScalarResult();
    }
}
