<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\Collection;
use App\Entity\CohortInterface;

/**
 * Interface CoursesEntityInterface
 */
interface CohortsEntityInterface
{
    public function setCohorts(Collection $cohorts): void;

    public function addCohort(CohortInterface $cohort): void;

    public function removeCohort(CohortInterface $cohort): void;

    public function getCohorts(): Collection;
}
