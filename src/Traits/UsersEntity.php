<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\UserInterface;

/**
 * Class UsersEntity
 */
trait UsersEntity
{
    protected Collection $users;

    public function setUsers(Collection $users): void
    {
        $this->users = new ArrayCollection();

        foreach ($users as $user) {
            $this->addUser($user);
        }
    }

    public function addUser(UserInterface $user): void
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }
    }

    public function removeUser(UserInterface $user): void
    {
        $this->users->removeElement($user);
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }
}
