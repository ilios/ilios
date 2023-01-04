<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\SessionTypeInterface;

/**
 * Class SessionTypesEntity
 */
trait SessionTypesEntity
{
    protected Collection $sessionTypes;

    public function setSessionTypes(Collection $sessionTypes)
    {
        $this->sessionTypes = new ArrayCollection();

        foreach ($sessionTypes as $sessionType) {
            $this->addSessionType($sessionType);
        }
    }

    public function addSessionType(SessionTypeInterface $sessionType)
    {
        if (!$this->sessionTypes->contains($sessionType)) {
            $this->sessionTypes->add($sessionType);
        }
    }

    public function removeSessionType(SessionTypeInterface $sessionType)
    {
        $this->sessionTypes->removeElement($sessionType);
    }

    public function getSessionTypes(): Collection
    {
        return $this->sessionTypes;
    }
}
