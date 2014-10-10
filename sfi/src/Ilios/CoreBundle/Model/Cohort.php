<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\IdentifiableTrait;

use Ilios\CoreBundle\Model\CourseInterface;
use Ilios\CoreBundle\Model\ProgramYearInterface;

/**
 * Class Cohort
 * @package Ilios\CoreBundle\Model
 */
class Cohort implements CohortInterface
{
    use IdentifiableTrait;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var ProgramYearInterface
     */
    protected $programYear;

    /**
     * @var CourseInterface[]|ArrayCollection
     */
    protected $courses;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->courses = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @param ProgramYearInterface $programYear
     */
    public function setProgramYear(ProgramYearInterface $programYear = null)
    {
        $this->programYear = $programYear;
    }

    /**
     * @return ProgramYearInterface
     */
    public function getProgramYear()
    {
        return $this->programYear;
    }

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
        return $this->courses;
    }
}
