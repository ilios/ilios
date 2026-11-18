<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\CohortInterface;
use Doctrine\Common\Collections\Collection;

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
