<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Interface AdministratorsEntityInterface
 */
interface AdministratorsEntityInterface
{
    /**
     * @param Collection $administrators
     */
    public function setAdministrators(Collection $administrators);

    /**
     * @param UserInterface $administrator
     */
    public function addAdministrator(UserInterface $administrator);

    /**
     * @param UserInterface $administrator
     */
    public function removeAdministrator(UserInterface $administrator);

    /**
    * @return UserInterface[]|ArrayCollection
    */
    public function getAdministrators();
}
