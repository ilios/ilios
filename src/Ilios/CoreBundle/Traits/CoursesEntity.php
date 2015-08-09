<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\CourseInterface;

/**
 * Class CoursesEntity
 * @package Ilios\CoreBundle\Traits
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
        $this->courses->add($course);
    }

    /**
    * @return CourseInterface[]|ArrayCollection
    */
    public function getCourses()
    {
        //criteria not 100% reliale on many to many relationships
        //fix in https://github.com/doctrine/doctrine2/pull/1399
        // $criteria = Criteria::create()->where(Criteria::expr()->eq("deleted", false));
        // return new ArrayCollection($this->courses->matching($criteria)->getValues());
        
        $arr = $this->courses->filter(function ($entity) {
            return !$entity->isDeleted();
        })->toArray();
        
        $reIndexed = array_values($arr);
        
        return new ArrayCollection($reIndexed);
    }
}
