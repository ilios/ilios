<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\Collection;
use App\Entity\CourseInterface;

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
