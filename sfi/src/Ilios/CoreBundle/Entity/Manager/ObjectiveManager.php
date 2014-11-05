<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\ObjectiveManager as BaseObjectiveManager;
use Ilios\CoreBundle\Model\ObjectiveInterface;

class ObjectiveManager extends BaseObjectiveManager
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
    public function findObjectiveBy(array $criteria, array $orderBy = null)
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * Previously known as findAllBy()
     *
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return ObjectiveInterface[]|Collection
     */
    public function findObjectivesBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param ObjectiveInterface $objective
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateObjective(ObjectiveInterface $objective, $andFlush = true)
    {
        $this->em->persist($objective);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param ObjectiveInterface $objective
     *
     * @return void
     */
    public function deleteObjective(ObjectiveInterface $objective)
    {
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
}
