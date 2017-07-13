<?php

namespace Ilios\CoreBundle\Entity\Manager;

/**
 * Class CourseLearningMaterialManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CourseLearningMaterialManager extends DTOManager
{
    /**
     * @return integer
     */
    public function getTotalCourseLearningMaterialCount()
    {
        return $this->em->createQuery('SELECT COUNT(l.id) FROM IliosCoreBundle:CourseLearningMaterial l')
            ->getSingleScalarResult();
    }
}
