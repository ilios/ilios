<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\CohortInterface;

/**
 * Class CohortsEntity
 */
trait CohortsEntity
{
    public function setCohorts(Collection $cohorts)
    {
        $this->cohorts = new ArrayCollection();

        foreach ($cohorts as $cohort) {
            $this->addCohort($cohort);
        }
    }

    public function addCohort(CohortInterface $cohort)
    {
        if (!$this->cohorts->contains($cohort)) {
            $this->cohorts->add($cohort);
        }
    }

    public function removeCohort(CohortInterface $cohort)
    {
        $this->cohorts->removeElement($cohort);
    }

    /**
    * @return CohortInterface[]|ArrayCollection
    */
    public function getCohorts(): Collection
    {
        return $this->cohorts;
    }
}
