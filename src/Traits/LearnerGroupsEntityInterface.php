<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\LearnerGroupInterface;

/**
 * Interface LearnerGroupsEntityInterface
 */
interface LearnerGroupsEntityInterface
{
    public function setLearnerGroups(Collection $learnerGroups);

    public function addLearnerGroup(LearnerGroupInterface $learnerGroup);

    public function removeLearnerGroup(LearnerGroupInterface $learnerGroup);

    /**
    * @return LearnerGroupInterface[]|ArrayCollection
    */
    public function getLearnerGroups(): Collection;
}
