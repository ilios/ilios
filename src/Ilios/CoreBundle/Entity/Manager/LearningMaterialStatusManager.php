<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\LearningMaterialStatusInterface;

/**
 * Class LearningMaterialStatusManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class LearningMaterialStatusManager extends BaseManager implements LearningMaterialStatusManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findLearningMaterialStatusBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function deleteLearningMaterialStatus(
        LearningMaterialStatusInterface $learningMaterialStatus
    ) {
        $this->em->remove($learningMaterialStatus);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createLearningMaterialStatus()
    {
        $class = $this->getClass();
        return new $class();
    }
}
