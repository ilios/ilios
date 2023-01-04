<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\UserInterface;

/**
 * Class LearnersEntity
 */
trait LearnersEntity
{
    protected Collection $learners;

    public function setLearners(Collection $learners)
    {
        $this->learners = new ArrayCollection();

        foreach ($learners as $learner) {
            $this->addLearner($learner);
        }
    }

    public function addLearner(UserInterface $learner)
    {
        if (!$this->learners->contains($learner)) {
            $this->learners->add($learner);
        }
    }

    public function removeLearner(UserInterface $learner)
    {
        $this->learners->removeElement($learner);
    }

    public function getLearners(): Collection
    {
        return $this->learners;
    }
}
