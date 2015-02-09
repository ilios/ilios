<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\ProgramYearStewardInterface;

/**
 * ProgramYearSteward manager service.
 * Class ProgramYearStewardManager
 * @package Ilios\CoreBundle\Manager
 */
class ProgramYearStewardManager implements ProgramYearStewardManagerInterface
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
     * @return ProgramYearStewardInterface
     */
    public function findProgramYearStewardBy(
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
     * @return ProgramYearStewardInterface[]|Collection
     */
    public function findProgramYearStewardsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param ProgramYearStewardInterface $programYearSteward
     * @param bool $andFlush
     */
    public function updateProgramYearSteward(
        ProgramYearStewardInterface $programYearSteward,
        $andFlush = true
    ) {
        $this->em->persist($programYearSteward);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param ProgramYearStewardInterface $programYearSteward
     */
    public function deleteProgramYearSteward(
        ProgramYearStewardInterface $programYearSteward
    ) {
        $this->em->remove($programYearSteward);
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
     * @return ProgramYearStewardInterface
     */
    public function createProgramYearSteward()
    {
        $class = $this->getClass();
        return new $class();
    }
}
