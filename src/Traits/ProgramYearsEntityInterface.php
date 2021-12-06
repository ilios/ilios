<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\ProgramYearInterface;

/**
 * Interface ProgramYearsEntityInterface
 */
interface ProgramYearsEntityInterface
{
    public function setProgramYears(Collection $programYears);

    public function addProgramYear(ProgramYearInterface $programYear);

    public function removeProgramYear(ProgramYearInterface $programYear);

    public function getProgramYears(): Collection;
}
