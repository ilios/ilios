<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\ProgramInterface;

/**
 * Class ProgramsEntity
 */
trait ProgramsEntity
{
    protected Collection $programs;

    public function setPrograms(Collection $programs): void
    {
        $this->programs = new ArrayCollection();

        foreach ($programs as $program) {
            $this->addProgram($program);
        }
    }

    public function addProgram(ProgramInterface $program): void
    {
        if (!$this->programs->contains($program)) {
            $this->programs->add($program);
        }
    }

    public function removeProgram(ProgramInterface $program): void
    {
        $this->programs->removeElement($program);
    }

    public function getPrograms(): Collection
    {
        return $this->programs;
    }
}
