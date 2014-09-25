<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\CourseLearningMaterialInterface;

/**
 * Interface CourseLearningMaterialManagerInterface
 */
interface CourseLearningMaterialManagerInterface
{
    /** 
     *@return CourseLearningMaterialInterface
     */
    public function createCourseLearningMaterial();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CourseLearningMaterialInterface
     */
    public function findCourseLearningMaterialBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return CourseLearningMaterialInterface[]|Collection
     */
    public function findCourseLearningMaterialsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param CourseLearningMaterialInterface $courseLearningMaterial
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateCourseLearningMaterial(CourseLearningMaterialInterface $courseLearningMaterial, $andFlush = true);

    /**
     * @param CourseLearningMaterialInterface $courseLearningMaterial
     *
     * @return void
     */
    public function deleteCourseLearningMaterial(CourseLearningMaterialInterface $courseLearningMaterial);

    /**
     * @return string
     */
    public function getClass();
}
