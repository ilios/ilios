<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\ObjectiveInterface;

/**
 * Objective manager service.
 * Class ObjectiveManager
 * @package Ilios\CoreBundle\Manager
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
     * @return ObjectiveInterface[]|Collection
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
     */
    public function updateObjective(
        ObjectiveInterface $objective,
        $andFlush = true
    ) {
        $this->em->persist($objective);
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
