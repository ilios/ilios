<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\CompetencyManager as BaseCompetencyManager;
use Ilios\CoreBundle\Model\CompetencyInterface;

class CompetencyManager extends BaseCompetencyManager
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
     * @return CompetencyInterface
     */
    public function findCompetencyBy(array $criteria, array $orderBy = null)
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
     * @return CompetencyInterface[]|Collection
     */
    public function findCompetenciesBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param CompetencyInterface $competency
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateCompetency(CompetencyInterface $competency, $andFlush = true)
    {
        $this->em->persist($competency);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param CompetencyInterface $competency
     *
     * @return void
     */
    public function deleteCompetency(CompetencyInterface $competency)
    {
        $this->em->remove($competency);
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
