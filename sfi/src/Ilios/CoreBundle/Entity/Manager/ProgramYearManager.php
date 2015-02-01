<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\ProgramYearInterface;

/**
 * ProgramYear manager service.
 * Class ProgramYearManager
 * @package Ilios\CoreBundle\Manager
 */
class ProgramYearManager implements ProgramYearManagerInterface
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
     * @return ProgramYearInterface
     */
    public function findProgramYearBy(array $criteria, array $orderBy = null)
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ProgramYearInterface[]|Collection
     */
    public function findProgramYearsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param ProgramYearInterface $programYear
     * @param bool $andFlush
     */
    public function updateProgramYear(ProgramYearInterface $programYear, $andFlush = true)
    {
        $this->em->persist($programYear);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param ProgramYearInterface $programYear
     */
    public function deleteProgramYear(ProgramYearInterface $programYear)
    {
        $this->em->remove($programYear);
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
     * @return ProgramYearInterface
     */
    public function createProgramYear()
    {
        $class = $this->getClass();
        return new $class();
    }
}
