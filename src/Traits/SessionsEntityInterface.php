<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\SessionInterface;

/**
 * Interface SessionsEntityInterface
 */
interface SessionsEntityInterface
{
    public function setSessions(Collection $sessions);

    public function addSession(SessionInterface $session);

    public function removeSession(SessionInterface $session);

    public function getSessions(): Collection;
}
