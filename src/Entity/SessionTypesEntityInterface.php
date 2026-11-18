<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\SessionTypeInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Interface SessionTypesEntityInterface
 */
interface SessionTypesEntityInterface
{
    public function setSessionTypes(Collection $sessionTypes): void;

    public function addSessionType(SessionTypeInterface $sessionType): void;

    public function removeSessionType(SessionTypeInterface $sessionType): void;

    public function getSessionTypes(): Collection;
}
