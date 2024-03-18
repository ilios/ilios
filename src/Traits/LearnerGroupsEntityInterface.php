<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\Collection;
use App\Entity\LearnerGroupInterface;

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
