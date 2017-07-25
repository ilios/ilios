<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Interface DirectorsEntityInterface
 */
interface DirectorsEntityInterface
{
    /**
     * @param Collection $directors
     */
    public function setDirectors(Collection $directors);

    /**
     * @param UserInterface $director
     */
    public function addDirector(UserInterface $director);

    /**
     * @param UserInterface $director
     */
    public function removeDirector(UserInterface $director);

    /**
    * @return UserInterface[]|ArrayCollection
    */
    public function getDirectors();
}
