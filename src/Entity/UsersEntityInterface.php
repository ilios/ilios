<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\UserInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Interface UsersEntityInterface
 */
interface UsersEntityInterface
{
    public function setUsers(Collection $users): void;

    public function addUser(UserInterface $user): void;

    public function removeUser(UserInterface $user): void;

    public function getUsers(): Collection;
}
