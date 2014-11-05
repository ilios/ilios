<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\LearningMaterialManager as BaseLearningMaterialManager;
use Ilios\CoreBundle\Model\LearningMaterialInterface;

class LearningMaterialManager extends BaseLearningMaterialManager
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
     * @return LearningMaterialInterface
     */
    public function findLearningMaterialBy(array $criteria, array $orderBy = null)
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
     * @return LearningMaterialInterface[]|Collection
     */
    public function findLearningMaterialsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param LearningMaterialInterface $learningMaterial
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateLearningMaterial(LearningMaterialInterface $learningMaterial, $andFlush = true)
    {
        $this->em->persist($learningMaterial);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param LearningMaterialInterface $learningMaterial
     *
     * @return void
     */
    public function deleteLearningMaterial(LearningMaterialInterface $learningMaterial)
    {
        $this->em->remove($learningMaterial);
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
