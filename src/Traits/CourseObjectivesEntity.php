<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\CourseObjectiveInterface;

/**
 * Class CourseObjectivesEntity
 */
trait CourseObjectivesEntity
{
    public function setCourseObjectives(Collection $courseObjectives = null): void
    {
        $this->courseObjectives = new ArrayCollection();
        if (is_null($courseObjectives)) {
            return;
        }

        foreach ($courseObjectives as $courseObjective) {
            $this->addCourseObjective($courseObjective);
        }
    }

    public function addCourseObjective(CourseObjectiveInterface $courseObjective): void
    {
        if (!$this->courseObjectives->contains($courseObjective)) {
            $this->courseObjectives->add($courseObjective);
        }
    }

    public function removeCourseObjective(CourseObjectiveInterface $courseObjective): void
    {
        $this->courseObjectives->removeElement($courseObjective);
    }

    public function getCourseObjectives(): Collection
    {
        return $this->courseObjectives;
    }
}
