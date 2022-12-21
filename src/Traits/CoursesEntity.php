<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\CourseInterface;

/**
 * Class CoursesEntity
 */
trait CoursesEntity
{
    protected Collection $courses;

    public function setCourses(Collection $courses)
    {
        $this->courses = new ArrayCollection();

        foreach ($courses as $course) {
            $this->addCourse($course);
        }
    }

    public function addCourse(CourseInterface $course)
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
        }
    }

    public function removeCourse(CourseInterface $course)
    {
        $this->courses->removeElement($course);
    }

    public function getCourses(): Collection
    {
        return $this->courses;
    }
}
