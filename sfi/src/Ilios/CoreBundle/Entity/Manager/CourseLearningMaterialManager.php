<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\CourseLearningMaterialManager as BaseCourseLearningMaterialManager;
use Ilios\CoreBundle\Model\CourseLearningMaterialInterface;

class CourseLearningMaterialManager extends BaseCourseLearningMaterialManager
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
     * Previously known as findAllBy()
     *
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
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
     *
     * @return void
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
     *
     * @return void
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
}
