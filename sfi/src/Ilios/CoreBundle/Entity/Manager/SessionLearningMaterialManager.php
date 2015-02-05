<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\SessionLearningMaterialInterface;

/**
 * SessionLearningMaterial manager service.
 * Class SessionLearningMaterialManager
 * @package Ilios\CoreBundle\Manager
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
     * @param EntityManager $em
     * @param string $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em         = $em;
        $this->class      = $class;
        $this->repository = $em->getRepository($class);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return SessionLearningMaterialInterface
     */
    public function findSessionLearningMaterialBy(array $criteria, array $orderBy = null)
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return SessionLearningMaterialInterface[]|Collection
     */
    public function findSessionLearningMaterialsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param SessionLearningMaterialInterface $sessionLearningMaterial
     * @param bool $andFlush
     */
    public function updateSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial, $andFlush = true)
    {
        $this->em->persist($sessionLearningMaterial);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param SessionLearningMaterialInterface $sessionLearningMaterial
     */
    public function deleteSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial)
    {
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
