<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\IlmSessionInterface;

/**
 * Class IlmSessionsEntity
 */
trait IlmSessionsEntity
{
    protected Collection $ilmSessions;

    public function setIlmSessions(Collection $ilmSessions)
    {
        $this->ilmSessions = new ArrayCollection();

        foreach ($ilmSessions as $ilmSession) {
            $this->addIlmSession($ilmSession);
        }
    }

    public function addIlmSession(IlmSessionInterface $ilmSession)
    {
        if (!$this->ilmSessions->contains($ilmSession)) {
            $this->ilmSessions->add($ilmSession);
        }
    }

    public function removeIlmSession(IlmSessionInterface $ilmSession)
    {
        $this->ilmSessions->removeElement($ilmSession);
    }

    public function getIlmSessions(): Collection
    {
        return $this->ilmSessions;
    }
}
