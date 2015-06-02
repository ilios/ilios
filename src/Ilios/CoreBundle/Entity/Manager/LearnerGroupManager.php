<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\LearnerGroupInterface;

/**
 * Class LearnerGroupManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class LearnerGroupManager implements LearnerGroupManagerInterface
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
     * @return LearnerGroupInterface
     */
    public function findLearnerGroupBy(
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
     * @return ArrayCollection|LearnerGroupInterface[]
     */
    public function findLearnerGroupsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param LearnerGroupInterface $learnerGroup
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateLearnerGroup(
        LearnerGroupInterface $learnerGroup,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($learnerGroup);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($learnerGroup));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param LearnerGroupInterface $learnerGroup
     */
    public function deleteLearnerGroup(
        LearnerGroupInterface $learnerGroup
    ) {
        $this->em->remove($learnerGroup);
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
     * @return LearnerGroupInterface
     */
    public function createLearnerGroup()
    {
        $class = $this->getClass();
        return new $class();
    }
}
