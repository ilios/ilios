<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\Collection;
use App\Entity\InstructorGroupInterface;

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
