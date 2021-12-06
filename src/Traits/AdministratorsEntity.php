<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\UserInterface;

/**
 * Class AdministratorsEntity
 */
trait AdministratorsEntity
{
    public function setAdministrators(Collection $administrators)
    {
        $this->administrators = new ArrayCollection();

        foreach ($administrators as $administrator) {
            $this->addAdministrator($administrator);
        }
    }

    public function addAdministrator(UserInterface $administrator)
    {
        if (!$this->administrators->contains($administrator)) {
            $this->administrators->add($administrator);
        }
    }

    public function removeAdministrator(UserInterface $administrator)
    {
        $this->administrators->removeElement($administrator);
    }

    /**
    * @return UserInterface[]|ArrayCollection
    */
    public function getAdministrators(): Collection
    {
        return $this->administrators;
    }
}
