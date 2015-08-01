<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\PermissionInterface;

/**
 * Interface PermissionManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface PermissionManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return PermissionInterface
     */
    public function findPermissionBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|PermissionInterface[]
     */
    public function findPermissionsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param PermissionInterface $permission
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updatePermission(
        PermissionInterface $permission,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param PermissionInterface $permission
     *
     * @return void
     */
    public function deletePermission(
        PermissionInterface $permission
    );

    /**
     * @return PermissionInterface
     */
    public function createPermission();
}
