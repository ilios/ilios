<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IndexableCoursesEntityInterface;
use App\Traits\ObjectiveRelationshipInterface;

/**
 * Interface CourseObjectiveInterface
 */
interface CourseObjectiveInterface extends
    ObjectiveRelationshipInterface,
    IndexableCoursesEntityInterface
{
    /**
     * @param CourseInterface $course
     */
    public function setCourse(CourseInterface $course): void;

    /**
     * @return CourseInterface
     */
    public function getCourse(): CourseInterface;
}
