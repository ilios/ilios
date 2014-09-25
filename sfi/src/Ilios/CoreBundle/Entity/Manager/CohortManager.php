<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\CohortManager as BaseCohortManager;
use Ilios\CoreBundle\Model\CohortInterface;

class CohortManager extends BaseCohortManager
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
    public function findCohortBy(array $criteria, array $orderBy = null)
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * Previously known as findAllBy()
     *
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return CohortInterface[]|Collection
     */
    public function findCohortsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param CohortInterface $cohort
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateCohort(CohortInterface $cohort, $andFlush = true)
    {
        $this->em->persist($cohort);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param CohortInterface $cohort
     *
     * @return void
     */
    public function deleteCohort(CohortInterface $cohort)
    {
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
}
