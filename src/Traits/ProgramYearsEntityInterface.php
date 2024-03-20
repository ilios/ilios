<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\Collection;
use App\Entity\ProgramYearInterface;

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
