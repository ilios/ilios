<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\ProgramYearManager as BaseProgramYearManager;
use Ilios\CoreBundle\Model\ProgramYearInterface;

class ProgramYearManager extends BaseProgramYearManager
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
     * Previously known as findAllBy()
     *
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
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
     *
     * @return void
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
     *
     * @return void
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
}
