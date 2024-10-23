<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\Collection;
use App\Entity\ProgramInterface;

/**
 * Interface ProgramsEntityInterface
 */
interface ProgramsEntityInterface
{
    public function setPrograms(Collection $programs): void;

    public function addProgram(ProgramInterface $program): void;

    public function removeProgram(ProgramInterface $program): void;

    public function getPrograms(): Collection;
}
