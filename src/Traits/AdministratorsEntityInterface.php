<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\UserInterface;

/**
 * Interface AdministratorsEntityInterface
 */
interface AdministratorsEntityInterface
{
    public function setAdministrators(Collection $administrators);

    public function addAdministrator(UserInterface $administrator);

    public function removeAdministrator(UserInterface $administrator);

    /**
    * @return UserInterface[]|ArrayCollection
    */
    public function getAdministrators();
}
