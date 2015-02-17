<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;
use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * Interface UserRoleInterface
 * @package Ilios\CoreBundle\Entity
 */
interface UserRoleInterface extends IdentifiableEntityInterface, TitledEntityInterface, RoleInterface
{
    /**
     * @param Collection $users
     */
    public function setUsers(Collection $users);

    /**
     * @param UserInterface $user
     */
    public function addUser(UserInterface $user);

    /**
     * @return ArrayCollection|UserInterface[]
     */
    public function getUsers();
}
