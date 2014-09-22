<?php

namespace Ilios\CoreBundle\Model;

use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * Interface UserRoleInterface
 */
interface UserRoleInterface 
{
    public function getUserRoleId();

    public function setTitle($title);

    public function getTitle();

    public function addUser(\Ilios\CoreBundle\Model\User $users);

    public function removeUser(\Ilios\CoreBundle\Model\User $users);

    public function getUsers();

    public function getRole();
}
