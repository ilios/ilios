<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\SessionInterface;

/**
 * Class SessionsEntity
 */
trait SessionsEntity
{
    protected Collection $sessions;

    public function setSessions(Collection $sessions)
    {
        $this->sessions = new ArrayCollection();

        foreach ($sessions as $session) {
            $this->addSession($session);
        }
    }

    public function addSession(SessionInterface $session)
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
        }
    }

    public function removeSession(SessionInterface $session)
    {
        $this->sessions->removeElement($session);
    }

    public function getSessions(): Collection
    {
        return $this->sessions;
    }
}
