<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\LearningMaterialUserRoleInterface;

/**
 * Class LearningMaterialUserRoleManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class LearningMaterialUserRoleManager implements LearningMaterialUserRoleManagerInterface
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
     * @return LearningMaterialUserRoleInterface
     */
    public function findLearningMaterialUserRoleBy(
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
     * @return ArrayCollection|LearningMaterialUserRoleInterface[]
     */
    public function findLearningMaterialUserRolesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param LearningMaterialUserRoleInterface $learningMaterialUserRole
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateLearningMaterialUserRole(
        LearningMaterialUserRoleInterface $learningMaterialUserRole,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($learningMaterialUserRole);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($learningMaterialUserRole));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param LearningMaterialUserRoleInterface $learningMaterialUserRole
     */
    public function deleteLearningMaterialUserRole(
        LearningMaterialUserRoleInterface $learningMaterialUserRole
    ) {
        $this->em->remove($learningMaterialUserRole);
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
     * @return LearningMaterialUserRoleInterface
     */
    public function createLearningMaterialUserRole()
    {
        $class = $this->getClass();
        return new $class();
    }
}
