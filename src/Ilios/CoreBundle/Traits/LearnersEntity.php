<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class LearnersEntity
 */
trait LearnersEntity
{
    /**
     * @param Collection $learners
     */
    public function setLearners(Collection $learners)
    {
        $this->learners = new ArrayCollection();

        foreach ($learners as $learner) {
            $this->addLearner($learner);
        }
    }

    /**
     * @param UserInterface $learner
     */
    public function addLearner(UserInterface $learner)
    {
        if (!$this->learners->contains($learner)) {
            $this->learners->add($learner);
        }
    }

    /**
     * @param UserInterface $learner
     */
    public function removeLearner(UserInterface $learner)
    {
        $this->learners->removeElement($learner);
    }

    /**
    * @return UserInterface[]|ArrayCollection
    */
    public function getLearners()
    {
        return $this->learners;
    }
}
