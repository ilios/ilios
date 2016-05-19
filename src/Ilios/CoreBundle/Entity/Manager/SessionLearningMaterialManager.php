<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\SessionLearningMaterialInterface;

/**
 * Class SessionLearningMaterialManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class SessionLearningMaterialManager extends BaseManager implements SessionLearningMaterialManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findSessionLearningMaterialBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findSessionLearningMaterialsBy(
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
     * {@inheritdoc}
     */
    public function deleteSessionLearningMaterial(
        SessionLearningMaterialInterface $sessionLearningMaterial
    ) {
        $this->em->remove($sessionLearningMaterial);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createSessionLearningMaterial()
    {
        $class = $this->getClass();
        return new $class();
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalSessionLearningMaterialCount()
    {
        return $this->em->createQuery('SELECT COUNT(l.id) FROM IliosCoreBundle:SessionLearningMaterial l')
            ->getSingleScalarResult();
    }
}
