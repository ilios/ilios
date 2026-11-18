<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\SessionInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Interface SessionsEntityInterface
 */
interface SessionsEntityInterface
{
    public function setSessions(Collection $sessions): void;

    public function addSession(SessionInterface $session): void;

    public function removeSession(SessionInterface $session): void;

    public function getSessions(): Collection;
}
