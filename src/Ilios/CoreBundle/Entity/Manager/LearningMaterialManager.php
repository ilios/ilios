<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\LearningMaterialInterface;

/**
 * LearningMaterial manager service.
 * Class LearningMaterialManager
 * @package Ilios\CoreBundle\Manager
 */
class LearningMaterialManager implements LearningMaterialManagerInterface
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
     * @return LearningMaterialInterface[]|Collection
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
     */
    public function updateLearningMaterial(
        LearningMaterialInterface $learningMaterial,
        $andFlush = true
    ) {
        $this->em->persist($learningMaterial);
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
     * @return string
     */
    public function getClass()
    {
        return $this->class;
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
