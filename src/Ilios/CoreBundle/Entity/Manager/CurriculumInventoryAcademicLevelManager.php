<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevelInterface;

/**
 * Class CurriculumInventoryAcademicLevelManager
 * @package Ilios\CoreBundle\Entity\Manager
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
     * @param Registry $em
     * @param string $class
     */
    public function __construct(Registry $em, $class)
    {
        $this->em         = $em->getManagerForClass($class);
        $this->class      = $class;
        $this->repository = $em->getRepository($class);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CurriculumInventoryAcademicLevelInterface
     */
    public function findCurriculumInventoryAcademicLevelBy(
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
     * @return ArrayCollection|CurriculumInventoryAcademicLevelInterface[]
     */
    public function findCurriculumInventoryAcademicLevelsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param CurriculumInventoryAcademicLevelInterface $curriculumInventoryAcademicLevel
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateCurriculumInventoryAcademicLevel(
        CurriculumInventoryAcademicLevelInterface $curriculumInventoryAcademicLevel,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($curriculumInventoryAcademicLevel);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($curriculumInventoryAcademicLevel));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param CurriculumInventoryAcademicLevelInterface $curriculumInventoryAcademicLevel
     */
    public function deleteCurriculumInventoryAcademicLevel(
        CurriculumInventoryAcademicLevelInterface $curriculumInventoryAcademicLevel
    ) {
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
