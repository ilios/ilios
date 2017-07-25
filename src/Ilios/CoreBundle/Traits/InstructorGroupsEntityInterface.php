<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\InstructorGroupInterface;

/**
 * Interface InstructorGroupsEntityInterface
 */
interface InstructorGroupsEntityInterface
{
    /**
     * @param Collection $instructorGroups
     */
    public function setInstructorGroups(Collection $instructorGroups);

    /**
     * @param InstructorGroupInterface $instructorGroup
     */
    public function addInstructorGroup(InstructorGroupInterface $instructorGroup);

    /**
     * @param InstructorGroupInterface $instructorGroup
     */
    public function removeInstructorGroup(InstructorGroupInterface $instructorGroup);

    /**
    * @return InstructorGroupInterface[]|ArrayCollection
    */
    public function getInstructorGroups();
}
