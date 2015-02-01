<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\GroupInterface;

/**
 * Group manager service.
 * Class GroupManager
 * @package Ilios\CoreBundle\Manager
 */
class GroupManager implements GroupManagerInterface
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

    /**
     * @return GroupInterface
     */
    public function createGroup()
    {
        $class = $this->getClass();
        return new $class();
    }
}
