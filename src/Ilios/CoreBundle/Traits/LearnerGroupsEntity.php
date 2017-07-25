<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\LearnerGroupInterface;

/**
 * Class LearnerGroupsEntity
 */
trait LearnerGroupsEntity
{
    /**
     * @param Collection $learnerGroups
     */
    public function setLearnerGroups(Collection $learnerGroups)
    {
        $this->learnerGroups = new ArrayCollection();

        foreach ($learnerGroups as $learnerGroup) {
            $this->addLearnerGroup($learnerGroup);
        }
    }

    /**
     * @param LearnerGroupInterface $learnerGroup
     */
    public function addLearnerGroup(LearnerGroupInterface $learnerGroup)
    {
        if (!$this->learnerGroups->contains($learnerGroup)) {
            $this->learnerGroups->add($learnerGroup);
        }
    }

    /**
     * @param LearnerGroupInterface $learnerGroup
     */
    public function removeLearnerGroup(LearnerGroupInterface $learnerGroup)
    {
        $this->learnerGroups->removeElement($learnerGroup);
    }

    /**
    * @return LearnerGroupInterface[]|ArrayCollection
    */
    public function getLearnerGroups()
    {
        return $this->learnerGroups;
    }
}
