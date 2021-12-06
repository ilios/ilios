<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\UserInterface;

/**
 * Interface LearnersEntityInterface
 */
interface LearnersEntityInterface
{
    public function setLearners(Collection $learners);

    public function addLearner(UserInterface $learner);

    public function removeLearner(UserInterface $learner);

    public function getLearners(): Collection;
}
