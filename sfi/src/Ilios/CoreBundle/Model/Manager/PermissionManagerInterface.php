<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\PermissionInterface;

/**
 * Interface PermissionManagerInterface
 */
interface PermissionManagerInterface
{
    /** 
     *@return PermissionInterface
     */
    public function createPermission();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return PermissionInterface
     */
    public function findPermissionBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return PermissionInterface[]|Collection
     */
    public function findPermissionsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param PermissionInterface $permission
     * @param bool $andFlush
     *
     * @return void
     */
    public function updatePermission(PermissionInterface $permission, $andFlush = true);

    /**
     * @param PermissionInterface $permission
     *
     * @return void
     */
    public function deletePermission(PermissionInterface $permission);

    /**
     * @return string
     */
    public function getClass();
}
