<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\UserInterface;

/**
 * Interface UsersEntityInterface
 */
interface UsersEntityInterface
{
    public function setUsers(Collection $users);

    public function addUser(UserInterface $user);

    public function removeUser(UserInterface $user);

    public function getUsers(): Collection;
}
