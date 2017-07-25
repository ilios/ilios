<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class InstructorsEntity
 */
trait InstructorsEntity
{
    /**
     * @param Collection $instructors
     */
    public function setInstructors(Collection $instructors)
    {
        $this->instructors = new ArrayCollection();

        foreach ($instructors as $instructor) {
            $this->addInstructor($instructor);
        }
    }

    /**
     * @param UserInterface $instructor
     */
    public function addInstructor(UserInterface $instructor)
    {
        if (!$this->instructors->contains($instructor)) {
            $this->instructors->add($instructor);
        }
    }

    /**
     * @param UserInterface $instructor
     */
    public function removeInstructor(UserInterface $instructor)
    {
        $this->instructors->removeElement($instructor);
    }

    /**
    * @return UserInterface[]|ArrayCollection
    */
    public function getInstructors()
    {
        return $this->instructors;
    }
}
