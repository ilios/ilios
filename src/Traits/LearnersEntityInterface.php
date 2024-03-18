<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\Collection;
use App\Entity\UserInterface;

/**
 * Interface LearnersEntityInterface
 */
interface LearnersEntityInterface
{
    public function setLearners(Collection $learners): void;

    public function addLearner(UserInterface $learner): void;

    public function removeLearner(UserInterface $learner): void;

    public function getLearners(): Collection;
}
