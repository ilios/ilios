<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\GroupManager as BaseGroupManager;
use Ilios\CoreBundle\Model\GroupInterface;

class GroupManager extends BaseGroupManager
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
     * @return GroupInterface
     */
    public function findGroupBy(array $criteria, array $orderBy = null)
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
     * @return GroupInterface[]|Collection
     */
    public function findGroupsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param GroupInterface $group
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateGroup(GroupInterface $group, $andFlush = true)
    {
        $this->em->persist($group);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param GroupInterface $group
     *
     * @return void
     */
    public function deleteGroup(GroupInterface $group)
    {
        $this->em->remove($group);
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
