<?php

namespace App\Traits;

use App\Entity\CourseInterface;

/**
 * Interface IndexableCoursesEntityInterface
 */
interface IndexableCoursesEntityInterface
{
    /**
     * Returns any course with a relationship to this entity
     * even deeply nested ones
     * @return CourseInterface[]
     */
    public function getIndexableCourses() : array;
}
