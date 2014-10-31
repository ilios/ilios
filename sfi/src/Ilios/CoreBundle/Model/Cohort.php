<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Ilios\CoreBundle\Traits\IdentifiableEntity;

use Ilios\CoreBundle\Model\CourseInterface;
use Ilios\CoreBundle\Model\ProgramYearInterface;
use Ilios\CoreBundle\Traits\TitledEntity;

/**
 * Class Cohort
 * @package Ilios\CoreBundle\Model
 *
 * @ORM\Entity
 * @ORM\Table(name="cohort")
 */
class Cohort implements CohortInterface
{
    use IdentifiableEntity;
    use TitledEntity;

    /**
     * @var ProgramYearInterface
     *
     * @ORM\ManyToOne(targetEntity="ProgramYear", inversedBy="cohorts")
     * @ORM\JoinColumn(name="program_year_id", referencedColumnName="program_year_id")
     */
    protected $programYear;

    /**
     * @var ArrayCollection|CourseInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Course", mappedBy="cohorts")
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
