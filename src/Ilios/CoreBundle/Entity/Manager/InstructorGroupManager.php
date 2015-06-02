<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\InstructorGroupInterface;

/**
 * Class InstructorGroupManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class InstructorGroupManager implements InstructorGroupManagerInterface
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
     * @return InstructorGroupInterface
     */
    public function findInstructorGroupBy(
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
     * @return ArrayCollection|InstructorGroupInterface[]
     */
    public function findInstructorGroupsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param InstructorGroupInterface $instructorGroup
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateInstructorGroup(
        InstructorGroupInterface $instructorGroup,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($instructorGroup);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($instructorGroup));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param InstructorGroupInterface $instructorGroup
     */
    public function deleteInstructorGroup(
        InstructorGroupInterface $instructorGroup
    ) {
        $this->em->remove($instructorGroup);
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
     * @return InstructorGroupInterface
     */
    public function createInstructorGroup()
    {
        $class = $this->getClass();
        return new $class();
    }
}
