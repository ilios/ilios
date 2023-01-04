<?php

declare(strict_types=1);

namespace App\Traits;

use App\Entity\CourseObjectiveInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Interface CourseObjectivesEntityInterface
 */
interface CourseObjectivesEntityInterface
{
    public function setCourseObjectives(Collection $courseObjectives = null): void;

    public function addCourseObjective(CourseObjectiveInterface $courseObjective): void;

    public function removeCourseObjective(CourseObjectiveInterface $courseObjective): void;

    public function getCourseObjectives(): Collection;
}
