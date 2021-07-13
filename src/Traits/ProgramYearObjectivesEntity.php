<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\ProgramYearObjectiveInterface;

/**
 * Class ProgramYearObjectivesEntity
 */
trait ProgramYearObjectivesEntity
{
    public function setProgramYearObjectives(Collection $programYearObjectives = null): void
    {
        $this->programYearObjectives = new ArrayCollection();
        if (is_null($programYearObjectives)) {
            return;
        }

        foreach ($programYearObjectives as $programYearObjective) {
            $this->addProgramYearObjective($programYearObjective);
        }
    }

    public function addProgramYearObjective(ProgramYearObjectiveInterface $programYearObjective): void
    {
        if (!$this->programYearObjectives->contains($programYearObjective)) {
            $this->programYearObjectives->add($programYearObjective);
        }
    }

    public function removeProgramYearObjective(ProgramYearObjectiveInterface $programYearObjective): void
    {
        $this->programYearObjectives->removeElement($programYearObjective);
    }

    /**
     * @return Collection|ProgramYearObjectiveInterface[]
     */
    public function getProgramYearObjectives(): Collection
    {
        return $this->programYearObjectives;
    }
}
