<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\UserInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Interface AdministratorsEntityInterface
 */
interface AdministratorsEntityInterface
{
    public function setAdministrators(Collection $administrators): void;

    public function addAdministrator(UserInterface $administrator): void;

    public function removeAdministrator(UserInterface $administrator): void;

    public function getAdministrators(): Collection;
}
