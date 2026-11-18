<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\IlmSessionInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Interface IlmSessionsEntityInterface
 */
interface IlmSessionsEntityInterface
{
    public function setIlmSessions(Collection $ilmSessions): void;

    public function addIlmSession(IlmSessionInterface $ilmSession): void;

    public function removeIlmSession(IlmSessionInterface $ilmSession): void;

    public function getIlmSessions(): Collection;
}
