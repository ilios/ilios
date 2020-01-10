<?php

namespace App\Entity;

use App\Traits\IndexableCoursesEntityInterface;

/**
 * Interface CourseLearningMaterialInterface
 */
interface CourseLearningMaterialInterface extends
    LearningMaterialRelationshipInterface,
    IndexableCoursesEntityInterface
{
    /**
     * @param CourseInterface $course
     */
    public function setCourse(CourseInterface $course);

    /**
     * @return CourseInterface|null
     */
    public function getCourse();
}
