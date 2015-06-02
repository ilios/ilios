<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\ObjectiveInterface;

/**
 * Class ObjectiveManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class ObjectiveManager implements ObjectiveManagerInterface
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
     * @return ObjectiveInterface
     */
    public function findObjectiveBy(
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
     * @return ArrayCollection|ObjectiveInterface[]
     */
    public function findObjectivesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param ObjectiveInterface $objective
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateObjective(
        ObjectiveInterface $objective,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($objective);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($objective));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param ObjectiveInterface $objective
     */
    public function deleteObjective(
        ObjectiveInterface $objective
    ) {
        $this->em->remove($objective);
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
     * @return ObjectiveInterface
     */
    public function createObjective()
    {
        $class = $this->getClass();
        return new $class();
    }
}
