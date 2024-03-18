<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\Collection;
use App\Entity\IlmSessionInterface;

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
