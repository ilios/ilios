<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\SessionLearningMaterialInterface;

/**
 * Class SessionLearningMaterialManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class SessionLearningMaterialManager implements SessionLearningMaterialManagerInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $class;

    /**
     * @param Registry $em
     * @param string $class
     */
    public function __construct(Registry $em, $class)
    {
        $this->em         = $em->getManagerForClass($class);
        $this->class      = $class;
        $this->repository = $em->getRepository($class);
    }

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
     * @return string
     */
    public function getClass()
    {
        return $this->class;
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
