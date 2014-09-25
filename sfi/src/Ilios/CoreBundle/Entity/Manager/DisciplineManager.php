<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\DisciplineManager as BaseDisciplineManager;
use Ilios\CoreBundle\Model\DisciplineInterface;

class DisciplineManager extends BaseDisciplineManager
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
    public function findDisciplineBy(array $criteria, array $orderBy = null)
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
     * @return DisciplineInterface[]|Collection
     */
    public function findDisciplinesBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param DisciplineInterface $discipline
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateDiscipline(DisciplineInterface $discipline, $andFlush = true)
    {
        $this->em->persist($discipline);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param DisciplineInterface $discipline
     *
     * @return void
     */
    public function deleteDiscipline(DisciplineInterface $discipline)
    {
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
}
