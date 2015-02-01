<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\GroupInterface;

/**
 * Interface GroupManagerInterface
 * @package Ilios\CoreBundle\Manager
 */
interface GroupManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return GroupInterface
     */
    public function findGroupBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return GroupInterface[]|Collection
     */
    public function findGroupsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param GroupInterface $group
     * @param bool $andFlush
     *
     * @return void
     */
     public function updateGroup(GroupInterface $group, $andFlush = true);

    /**
     * @param GroupInterface $group
     *
     * @return void
     */
    public function deleteGroup(GroupInterface $group);

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return GroupInterface
     */
    public function createGroup();
}