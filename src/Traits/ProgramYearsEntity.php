<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\ProgramYearInterface;

/**
 * Class ProgramYearsEntity
 */
trait ProgramYearsEntity
{
    protected Collection $programYears;

    public function setProgramYears(Collection $programYears)
    {
        $this->programYears = new ArrayCollection();

        foreach ($programYears as $programYear) {
            $this->addProgramYear($programYear);
        }
    }

    public function addProgramYear(ProgramYearInterface $programYear)
    {
        if (!$this->programYears->contains($programYear)) {
            $this->programYears->add($programYear);
        }
    }

    public function removeProgramYear(ProgramYearInterface $programYear)
    {
        $this->programYears->removeElement($programYear);
    }

    public function getProgramYears(): Collection
    {
        return $this->programYears;
    }
}
