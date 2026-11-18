<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\ProgramYearInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Interface ProgramYearsEntityInterface
 */
interface ProgramYearsEntityInterface
{
    public function setProgramYears(Collection $programYears): void;

    public function addProgramYear(ProgramYearInterface $programYear): void;

    public function removeProgramYear(ProgramYearInterface $programYear): void;

    public function getProgramYears(): Collection;
}
