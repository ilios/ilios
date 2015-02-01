<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\CourseLearningMaterialInterface;

/**
 * CourseLearningMaterial manager service.
 * Class CourseLearningMaterialManager
 * @package Ilios\CoreBundle\Manager
 */
class CourseLearningMaterialManager implements CourseLearningMaterialManagerInterface
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
     * @return CourseLearningMaterialInterface
     */
    public function findCourseLearningMaterialBy(array $criteria, array $orderBy = null)
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return CourseLearningMaterialInterface[]|Collection
     */
    public function findCourseLearningMaterialsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param CourseLearningMaterialInterface $courseLearningMaterial
     * @param bool $andFlush
     */
    public function updateCourseLearningMaterial(CourseLearningMaterialInterface $courseLearningMaterial, $andFlush = true)
    {
        $this->em->persist($courseLearningMaterial);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param CourseLearningMaterialInterface $courseLearningMaterial
     */
    public function deleteCourseLearningMaterial(CourseLearningMaterialInterface $courseLearningMaterial)
    {
        $this->em->remove($courseLearningMaterial);
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
     * @return CourseLearningMaterialInterface
     */
    public function createCourseLearningMaterial()
    {
        $class = $this->getClass();
        return new $class();
    }
}
