<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\CohortInterface;

/**
 * Interface DescribableEntityInterface
 */
interface CohortsEntityInterface
{
    public function setCohorts(Collection $cohorts);

    public function addCohort(CohortInterface $cohort);

    public function removeCohort(CohortInterface $cohort);

    public function getCohorts(): Collection;
}
