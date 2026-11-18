<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\LearnerGroupInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Interface LearnerGroupsEntityInterface
 */
interface LearnerGroupsEntityInterface
{
    public function setLearnerGroups(Collection $learnerGroups): void;

    public function addLearnerGroup(LearnerGroupInterface $learnerGroup): void;

    public function removeLearnerGroup(LearnerGroupInterface $learnerGroup): void;

    public function getLearnerGroups(): Collection;
}
