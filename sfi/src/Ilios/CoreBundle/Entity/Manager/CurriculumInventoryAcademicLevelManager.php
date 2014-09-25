<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\CurriculumInventoryAcademicLevelManager as BaseCurriculumInventoryAcademicLevelManager;
use Ilios\CoreBundle\Model\CurriculumInventoryAcademicLevelInterface;

class CurriculumInventoryAcademicLevelManager extends BaseCurriculumInventoryAcademicLevelManager
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
     * @return CurriculumInventoryAcademicLevelInterface
     */
    public function findCurriculumInventoryAcademicLevelBy(array $criteria, array $orderBy = null)
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
     * @return CurriculumInventoryAcademicLevelInterface[]|Collection
     */
    public function findCurriculumInventoryAcademicLevelsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param CurriculumInventoryAcademicLevelInterface $curriculumInventoryAcademicLevel
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateCurriculumInventoryAcademicLevel(CurriculumInventoryAcademicLevelInterface $curriculumInventoryAcademicLevel, $andFlush = true)
    {
        $this->em->persist($curriculumInventoryAcademicLevel);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param CurriculumInventoryAcademicLevelInterface $curriculumInventoryAcademicLevel
     *
     * @return void
     */
    public function deleteCurriculumInventoryAcademicLevel(CurriculumInventoryAcademicLevelInterface $curriculumInventoryAcademicLevel)
    {
        $this->em->remove($curriculumInventoryAcademicLevel);
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
