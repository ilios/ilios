<?php

declare(strict_types=1);

namespace App\Traits;

use App\Entity\ProgramYearObjectiveInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Interface ProgramYearObjectivesEntityInterface
 */
interface ProgramYearObjectivesEntityInterface
{
    public function setProgramYearObjectives(?Collection $programYearObjectives = null): void;

    public function addProgramYearObjective(ProgramYearObjectiveInterface $programYearObjective): void;

    public function removeProgramYearObjective(ProgramYearObjectiveInterface $programYearObjective): void;

    public function getProgramYearObjectives(): Collection;
}
