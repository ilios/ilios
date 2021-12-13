<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\ProgramInterface;

/**
 * Interface ProgramsEntityInterface
 */
interface ProgramsEntityInterface
{
    public function setPrograms(Collection $programs);

    public function addProgram(ProgramInterface $program);

    public function removeProgram(ProgramInterface $program);

    public function getPrograms(): Collection;
}
