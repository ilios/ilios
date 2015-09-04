<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\CourseLearningMaterialInterface;

/**
 * Class CourseLearningMaterialManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CourseLearningMaterialManager extends AbstractManager implements CourseLearningMaterialManagerInterface
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
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

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
    ) {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param CourseLearningMaterialInterface $courseLearningMaterial
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateCourseLearningMaterial(
        CourseLearningMaterialInterface $courseLearningMaterial,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($courseLearningMaterial);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($courseLearningMaterial));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param CourseLearningMaterialInterface $courseLearningMaterial
     */
    public function deleteCourseLearningMaterial(
        CourseLearningMaterialInterface $courseLearningMaterial
    ) {
        $this->em->remove($courseLearningMaterial);
        $this->em->flush();
    }

    /**
     * @return CourseLearningMaterialInterface
     */
    public function createCourseLearningMaterial()
    {
        $class = $this->getClass();
        return new $class();
    }
}
