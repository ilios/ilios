<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\Collection;
use App\Entity\UserInterface;

/**
 * Interface DirectorsEntityInterface
 */
interface DirectorsEntityInterface
{
    public function setDirectors(Collection $directors): void;

    public function addDirector(UserInterface $director): void;

    public function removeDirector(UserInterface $director): void;

    public function getDirectors(): Collection;
}
