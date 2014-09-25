<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\UserRoleInterface;

/**
 * Interface UserRoleManagerInterface
 */
interface UserRoleManagerInterface
{
    /** 
     *@return UserRoleInterface
     */
    public function createUserRole();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return UserRoleInterface
     */
    public function findUserRoleBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return UserRoleInterface[]|Collection
     */
    public function findUserRolesBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param UserRoleInterface $userRole
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateUserRole(UserRoleInterface $userRole, $andFlush = true);

    /**
     * @param UserRoleInterface $userRole
     *
     * @return void
     */
    public function deleteUserRole(UserRoleInterface $userRole);

    /**
     * @return string
     */
    public function getClass();
}
