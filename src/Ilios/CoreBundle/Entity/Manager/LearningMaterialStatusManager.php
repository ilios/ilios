<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\LearningMaterialStatusInterface;

/**
 * Class LearningMaterialStatusManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class LearningMaterialStatusManager extends AbstractManager implements LearningMaterialStatusManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return LearningMaterialStatusInterface
     */
    public function findLearningMaterialStatusBy(
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
     * @return ArrayCollection|LearningMaterialStatusInterface[]
     */
    public function findLearningMaterialStatusesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param LearningMaterialStatusInterface $learningMaterialStatus
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateLearningMaterialStatus(
        LearningMaterialStatusInterface $learningMaterialStatus,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($learningMaterialStatus);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($learningMaterialStatus));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param LearningMaterialStatusInterface $learningMaterialStatus
     */
    public function deleteLearningMaterialStatus(
        LearningMaterialStatusInterface $learningMaterialStatus
    ) {
        $this->em->remove($learningMaterialStatus);
        $this->em->flush();
    }

    /**
     * @return LearningMaterialStatusInterface
     */
    public function createLearningMaterialStatus()
    {
        $class = $this->getClass();
        return new $class();
    }
}
