<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\SessionLearningMaterialInterface;

/**
 * Class SessionLearningMaterialManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class SessionLearningMaterialManager extends AbstractManager implements SessionLearningMaterialManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return SessionLearningMaterialInterface
     */
    public function findSessionLearningMaterialBy(
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
     * @return ArrayCollection|SessionLearningMaterialInterface[]
     */
    public function findSessionLearningMaterialsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param SessionLearningMaterialInterface $sessionLearningMaterial
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateSessionLearningMaterial(
        SessionLearningMaterialInterface $sessionLearningMaterial,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($sessionLearningMaterial);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($sessionLearningMaterial));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param SessionLearningMaterialInterface $sessionLearningMaterial
     */
    public function deleteSessionLearningMaterial(
        SessionLearningMaterialInterface $sessionLearningMaterial
    ) {
        $this->em->remove($sessionLearningMaterial);
        $this->em->flush();
    }

    /**
     * @return SessionLearningMaterialInterface
     */
    public function createSessionLearningMaterial()
    {
        $class = $this->getClass();
        return new $class();
    }
}
