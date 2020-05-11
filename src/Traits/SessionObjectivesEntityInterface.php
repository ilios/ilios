<?php

declare(strict_types=1);

namespace App\Traits;

use App\Entity\SessionObjectiveInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Interface DescribableEntityInterface
 */
interface SessionObjectivesEntityInterface
{
    /**
     * @param Collection|SessionObjectiveInterface[] $sessionObjectives
     */
    public function setSessionObjectives(Collection $sessionObjectives = null): void;

    /**
     * @param SessionObjectiveInterface $sessionObjective
     */
    public function addSessionObjective(SessionObjectiveInterface $sessionObjective): void;

    /**
     * @param SessionObjectiveInterface $sessionObjective
     */
    public function removeSessionObjective(SessionObjectiveInterface $sessionObjective): void;

    /**
     * @return Collection|SessionObjectiveInterface[]
     */
    public function getSessionObjectives(): Collection;
}
