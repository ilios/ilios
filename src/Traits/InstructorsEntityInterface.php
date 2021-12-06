<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\UserInterface;

/**
 * Interface InstructorsEntityInterface
 */
interface InstructorsEntityInterface
{
    public function setInstructors(Collection $instructors);

    public function addInstructor(UserInterface $instructor);

    public function removeInstructor(UserInterface $instructor);

    public function getInstructors(): Collection;
}
