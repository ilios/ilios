<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\InstructorGroupInterface;

/**
 * Class InstructorGroupsEntity
 */
trait InstructorGroupsEntity
{
    /**
     * @param Collection $instructorGroups
     */
    public function setInstructorGroups(Collection $instructorGroups)
    {
        $this->instructorGroups = new ArrayCollection();

        foreach ($instructorGroups as $instructorGroup) {
            $this->addInstructorGroup($instructorGroup);
        }
    }

    /**
     * @param InstructorGroupInterface $instructorGroup
     */
    public function addInstructorGroup(InstructorGroupInterface $instructorGroup)
    {
        if (!$this->instructorGroups->contains($instructorGroup)) {
            $this->instructorGroups->add($instructorGroup);
        }
    }

    /**
     * @param InstructorGroupInterface $instructorGroup
     */
    public function removeInstructorGroup(InstructorGroupInterface $instructorGroup)
    {
        $this->instructorGroups->removeElement($instructorGroup);
    }

    /**
    * @return InstructorGroupInterface[]|ArrayCollection
    */
    public function getInstructorGroups()
    {
        return $this->instructorGroups;
    }
}
