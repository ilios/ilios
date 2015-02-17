<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\CohortInterface;

/**
 * Cohort manager service.
 * Class CohortManager
 * @package Ilios\CoreBundle\Manager
 */
class CohortManager implements CohortManagerInterface
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
     * @return CohortInterface
     */
    public function findCohortBy(
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
     * @return CohortInterface[]|Collection
     */
    public function findCohortsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param CohortInterface $cohort
     * @param bool $andFlush
     */
    public function updateCohort(
        CohortInterface $cohort,
        $andFlush = true
    ) {
        $this->em->persist($cohort);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param CohortInterface $cohort
     */
    public function deleteCohort(
        CohortInterface $cohort
    ) {
        $this->em->remove($cohort);
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
     * @return CohortInterface
     */
    public function createCohort()
    {
        $class = $this->getClass();
        return new $class();
    }
}
