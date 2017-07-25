<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\CourseInterface;

/**
 * Interface DescribableEntityInterface
 */
interface CoursesEntityInterface
{
    /**
     * @param Collection $courses
     */
    public function setCourses(Collection $courses);

    /**
     * @param CourseInterface $course
     */
    public function addCourse(CourseInterface $course);

    /**
     * @param CourseInterface $course
     */
    public function removeCourse(CourseInterface $course);

    /**
    * @return CourseInterface[]|ArrayCollection
    */
    public function getCourses();
}
