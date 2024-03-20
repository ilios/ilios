<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\Collection;
use App\Entity\SessionInterface;

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
