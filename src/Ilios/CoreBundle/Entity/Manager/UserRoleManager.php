<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\UserRoleInterface;

/**
 * Class UserRoleManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class UserRoleManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findUserRoleBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findUserRolesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @deprecated
     */
    public function updateUserRole(
        UserRoleInterface $userRole,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($userRole, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteUserRole(
        UserRoleInterface $userRole
    ) {
        $this->delete($userRole);
    }

    /**
     * @deprecated
     */
    public function createUserRole()
    {
        return $this->create();
    }
}
