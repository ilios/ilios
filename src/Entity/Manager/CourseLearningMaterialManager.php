<?php

declare(strict_types=1);

namespace App\Entity\Manager;

/**
 * Class CourseLearningMaterialManager
 */
class CourseLearningMaterialManager extends BaseManager
{
    /**
     * @return int
     */
    public function getTotalCourseLearningMaterialCount()
    {
        return $this->em->createQuery('SELECT COUNT(l.id) FROM App\Entity\CourseLearningMaterial l')
            ->getSingleScalarResult();
    }
}
