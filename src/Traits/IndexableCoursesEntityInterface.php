<?php

declare(strict_types=1);

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
     */
    public function getIndexableCourses(): array;
}
