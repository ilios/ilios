<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevelInterface;

/**
 * CurriculumInventoryAcademicLevel manager service.
 * Class CurriculumInventoryAcademicLevelManager
 * @package Ilios\CoreBundle\Manager
 */
class CurriculumInventoryAcademicLevelManager implements CurriculumInventoryAcademicLevelManagerInterface
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

    /**
     * @return CurriculumInventoryAcademicLevelInterface
     */
    public function createCurriculumInventoryAcademicLevel()
    {
        $class = $this->getClass();
        return new $class();
    }
}
