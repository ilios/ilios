<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\CourseInterface;

/**
 * Class CoursesEntity
 */
trait CoursesEntity
{
    /**
     * @param Collection $courses
     */
    public function setCourses(Collection $courses)
    {
        $this->courses = new ArrayCollection();

        foreach ($courses as $course) {
            $this->addCourse($course);
        }
    }

    /**
     * @param CourseInterface $course
     */
    public function addCourse(CourseInterface $course)
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
        }
    }

    /**
     * @param CourseInterface $course
     */
    public function removeCourse(CourseInterface $course)
    {
        $this->courses->removeElement($course);
    }

    /**
    * @return CourseInterface[]|ArrayCollection
    */
    public function getCourses()
    {
        return $this->courses;
    }
}
