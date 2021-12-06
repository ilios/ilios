<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\CourseInterface;

/**
 * Interface DescribableEntityInterface
 */
interface CoursesEntityInterface
{
    public function setCourses(Collection $courses);

    public function addCourse(CourseInterface $course);

    public function removeCourse(CourseInterface $course);

    /**
    * @return CourseInterface[]|ArrayCollection
    */
    public function getCourses(): Collection;
}
