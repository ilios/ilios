<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Interface UsersEntityInterface
 */
interface UsersEntityInterface
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
     * @param UserInterface $user
     */
    public function removeUser(UserInterface $user);

    /**
    * @return UserInterface[]|ArrayCollection
    */
    public function getUsers();
}
