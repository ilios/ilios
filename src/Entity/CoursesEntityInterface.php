<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\CourseInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Interface CoursesEntityInterface
 */
interface CoursesEntityInterface
{
    public function setCourses(Collection $courses): void;

    public function addCourse(CourseInterface $course): void;

    public function removeCourse(CourseInterface $course): void;

    public function getCourses(): Collection;
}
