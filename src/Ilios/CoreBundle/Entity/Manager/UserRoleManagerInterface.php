<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\UserRoleInterface;

/**
 * Interface UserRoleManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface UserRoleManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return UserRoleInterface
     */
    public function findUserRoleBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|UserRoleInterface[]
     */
    public function findUserRolesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param UserRoleInterface $userRole
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateUserRole(
        UserRoleInterface $userRole,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param UserRoleInterface $userRole
     *
     * @return void
     */
    public function deleteUserRole(
        UserRoleInterface $userRole
    );

    /**
     * @return UserRoleInterface
     */
    public function createUserRole();
}
