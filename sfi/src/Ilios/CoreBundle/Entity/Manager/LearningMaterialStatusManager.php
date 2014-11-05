<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\LearningMaterialStatusManager as BaseLearningMaterialStatusManager;
use Ilios\CoreBundle\Model\LearningMaterialStatusInterface;

class LearningMaterialStatusManager extends BaseLearningMaterialStatusManager
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
     * @return LearningMaterialStatusInterface
     */
    public function findLearningMaterialStatusBy(array $criteria, array $orderBy = null)
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
     * @return LearningMaterialStatusInterface[]|Collection
     */
    public function findLearningMaterialStatusesBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param LearningMaterialStatusInterface $learningMaterialStatus
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateLearningMaterialStatus(LearningMaterialStatusInterface $learningMaterialStatus, $andFlush = true)
    {
        $this->em->persist($learningMaterialStatus);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param LearningMaterialStatusInterface $learningMaterialStatus
     *
     * @return void
     */
    public function deleteLearningMaterialStatus(LearningMaterialStatusInterface $learningMaterialStatus)
    {
        $this->em->remove($learningMaterialStatus);
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
