<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\Collection;
use App\Entity\SessionTypeInterface;

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
