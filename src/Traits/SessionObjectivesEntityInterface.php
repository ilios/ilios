<?php

declare(strict_types=1);

namespace App\Traits;

use App\Entity\SessionObjectiveInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Interface SessionObjectivesEntityInterface
 */
interface SessionObjectivesEntityInterface
{
    public function setSessionObjectives(?Collection $sessionObjectives = null): void;

    public function addSessionObjective(SessionObjectiveInterface $sessionObjective): void;

    public function removeSessionObjective(SessionObjectiveInterface $sessionObjective): void;

    public function getSessionObjectives(): Collection;
}
