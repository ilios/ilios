<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\UserInterface;

/**
 * Interface DirectorsEntityInterface
 */
interface DirectorsEntityInterface
{
    public function setDirectors(Collection $directors);

    public function addDirector(UserInterface $director);

    public function removeDirector(UserInterface $director);

    /**
    * @return UserInterface[]|ArrayCollection
    */
    public function getDirectors(): Collection;
}
