<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\CourseLearningMaterialInterface;

/**
 * Interface CourseLearningMaterialManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface CourseLearningMaterialManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CourseLearningMaterialInterface
     */
    public function findCourseLearningMaterialBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|CourseLearningMaterialInterface[]
     */
    public function findCourseLearningMaterialsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param CourseLearningMaterialInterface $courseLearningMaterial
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateCourseLearningMaterial(
        CourseLearningMaterialInterface $courseLearningMaterial,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param CourseLearningMaterialInterface $courseLearningMaterial
     *
     * @return void
     */
    public function deleteCourseLearningMaterial(
        CourseLearningMaterialInterface $courseLearningMaterial
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return CourseLearningMaterialInterface
     */
    public function createCourseLearningMaterial();
}
