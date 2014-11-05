<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\InstructorGroupManager as BaseInstructorGroupManager;
use Ilios\CoreBundle\Model\InstructorGroupInterface;

class InstructorGroupManager extends BaseInstructorGroupManager
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
     * @return InstructorGroupInterface
     */
    public function findInstructorGroupBy(array $criteria, array $orderBy = null)
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
     * @return InstructorGroupInterface[]|Collection
     */
    public function findInstructorGroupsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param InstructorGroupInterface $instructorGroup
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateInstructorGroup(InstructorGroupInterface $instructorGroup, $andFlush = true)
    {
        $this->em->persist($instructorGroup);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param InstructorGroupInterface $instructorGroup
     *
     * @return void
     */
    public function deleteInstructorGroup(InstructorGroupInterface $instructorGroup)
    {
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
}
