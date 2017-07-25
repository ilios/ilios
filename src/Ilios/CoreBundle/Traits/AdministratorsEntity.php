<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class AdministratorsEntity
 */
trait AdministratorsEntity
{
    /**
     * @param Collection $administrators
     */
    public function setAdministrators(Collection $administrators)
    {
        $this->administrators = new ArrayCollection();

        foreach ($administrators as $administrator) {
            $this->addAdministrator($administrator);
        }
    }

    /**
     * @param UserInterface $administrator
     */
    public function addAdministrator(UserInterface $administrator)
    {
        if (!$this->administrators->contains($administrator)) {
            $this->administrators->add($administrator);
        }
    }

    /**
     * @param UserInterface $administrator
     */
    public function removeAdministrator(UserInterface $administrator)
    {
        $this->administrators->removeElement($administrator);
    }

    /**
    * @return UserInterface[]|ArrayCollection
    */
    public function getAdministrators()
    {
        return $this->administrators;
    }
}
