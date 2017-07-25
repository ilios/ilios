<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\CohortInterface;

/**
 * Class CohortsEntity
 */
trait CohortsEntity
{
    /**
     * @param Collection $cohorts
     */
    public function setCohorts(Collection $cohorts)
    {
        $this->cohorts = new ArrayCollection();

        foreach ($cohorts as $cohort) {
            $this->addCohort($cohort);
        }
    }

    /**
     * @param CohortInterface $cohort
     */
    public function addCohort(CohortInterface $cohort)
    {
        if (!$this->cohorts->contains($cohort)) {
            $this->cohorts->add($cohort);
        }
    }

    /**
     * @param CohortInterface $cohort
     */
    public function removeCohort(CohortInterface $cohort)
    {
        $this->cohorts->removeElement($cohort);
    }

    /**
    * @return CohortInterface[]|ArrayCollection
    */
    public function getCohorts()
    {
        return $this->cohorts;
    }
}
