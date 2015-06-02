<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\LearningMaterialStatusInterface;

/**
 * Class LearningMaterialStatusManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class LearningMaterialStatusManager implements LearningMaterialStatusManagerInterface
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
     * @return LearningMaterialStatusInterface
     */
    public function findLearningMaterialStatusBy(
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
     * @return ArrayCollection|LearningMaterialStatusInterface[]
     */
    public function findLearningMaterialStatusesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
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
     * @return string
     */
    public function getClass()
    {
        return $this->class;
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
