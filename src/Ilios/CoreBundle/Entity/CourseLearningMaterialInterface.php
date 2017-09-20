<?php

namespace Ilios\CoreBundle\Entity;

/**
 * Interface CourseLearningMaterialInterface
 */
interface CourseLearningMaterialInterface extends LearningMaterialRelationshipInterface
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
