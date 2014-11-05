<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\GroupInterface;

/**
 * Interface GroupManagerInterface
 */
interface GroupManagerInterface
{
    /** 
     *@return GroupInterface
     */
    public function createGroup();

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
     * @param int $limit
     * @param int $offset
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
}
