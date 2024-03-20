<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\Collection;
use App\Entity\UserInterface;

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
