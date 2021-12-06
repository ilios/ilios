<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\InstructorGroupInterface;

/**
 * Interface InstructorGroupsEntityInterface
 */
interface InstructorGroupsEntityInterface
{
    public function setInstructorGroups(Collection $instructorGroups);

    public function addInstructorGroup(InstructorGroupInterface $instructorGroup);

    public function removeInstructorGroup(InstructorGroupInterface $instructorGroup);

    /**
    * @return InstructorGroupInterface[]|ArrayCollection
    */
    public function getInstructorGroups(): Collection;
}
