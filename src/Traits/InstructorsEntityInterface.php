<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\Collection;
use App\Entity\UserInterface;

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
