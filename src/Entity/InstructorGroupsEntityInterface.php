<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\InstructorGroupInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Interface InstructorGroupsEntityInterface
 */
interface InstructorGroupsEntityInterface
{
    public function setInstructorGroups(Collection $instructorGroups): void;

    public function addInstructorGroup(InstructorGroupInterface $instructorGroup): void;

    public function removeInstructorGroup(InstructorGroupInterface $instructorGroup): void;

    public function getInstructorGroups(): Collection;
}
