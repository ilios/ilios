<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\LearningMaterialInterface;

/**
 * Class LearningMaterialManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class LearningMaterialManager extends AbstractManager implements LearningMaterialManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return LearningMaterialInterface
     */
    public function findLearningMaterialBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|LearningMaterialInterface[]
     */
    public function findLearningMaterialsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param LearningMaterialInterface $learningMaterial
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateLearningMaterial(
        LearningMaterialInterface $learningMaterial,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($learningMaterial);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($learningMaterial));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param LearningMaterialInterface $learningMaterial
     */
    public function deleteLearningMaterial(
        LearningMaterialInterface $learningMaterial
    ) {
        $this->em->remove($learningMaterial);
        $this->em->flush();
    }

    /**
     * @return LearningMaterialInterface
     */
    public function createLearningMaterial()
    {
        $class = $this->getClass();
        return new $class();
    }
}
