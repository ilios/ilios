<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\UserInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Interface InstructorsEntityInterface
 */
interface InstructorsEntityInterface
{
    public function setInstructors(Collection $instructors): void;

    public function addInstructor(UserInterface $instructor): void;

    public function removeInstructor(UserInterface $instructor): void;

    public function getInstructors(): Collection;
}
