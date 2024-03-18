<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\UserInterface;

/**
 * Class InstructorsEntity
 */
trait InstructorsEntity
{
    protected Collection $instructors;

    public function setInstructors(Collection $instructors): void
    {
        $this->instructors = new ArrayCollection();

        foreach ($instructors as $instructor) {
            $this->addInstructor($instructor);
        }
    }

    public function addInstructor(UserInterface $instructor): void
    {
        if (!$this->instructors->contains($instructor)) {
            $this->instructors->add($instructor);
        }
    }

    public function removeInstructor(UserInterface $instructor): void
    {
        $this->instructors->removeElement($instructor);
    }

    public function getInstructors(): Collection
    {
        return $this->instructors;
    }
}
