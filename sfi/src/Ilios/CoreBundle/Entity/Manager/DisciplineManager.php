<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\DisciplineInterface;

/**
 * Discipline manager service.
 * Class DisciplineManager
 * @package Ilios\CoreBundle\Manager
 */
class DisciplineManager implements DisciplineManagerInterface
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
     * @return DisciplineInterface
     */
    public function findDisciplineBy(
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
     * @return DisciplineInterface[]|Collection
     */
    public function findDisciplinesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param DisciplineInterface $discipline
     * @param bool $andFlush
     */
    public function updateDiscipline(
        DisciplineInterface $discipline,
        $andFlush = true
    ) {
        $this->em->persist($discipline);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param DisciplineInterface $discipline
     */
    public function deleteDiscipline(
        DisciplineInterface $discipline
    ) {
        $this->em->remove($discipline);
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
     * @return DisciplineInterface
     */
    public function createDiscipline()
    {
        $class = $this->getClass();
        return new $class();
    }
}
