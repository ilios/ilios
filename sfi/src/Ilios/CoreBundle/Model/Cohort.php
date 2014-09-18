<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cohort
 */
class Cohort
{
    /**
     * @var integer
     */
    private $cohortId;

    /**
     * @var string
     */
    private $title;

    /**
     * @var \Ilios\CoreBundle\Entity\ProgramYear
     */
    private $programYear;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $courses;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->courses = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get cohortId
     *
     * @return integer 
     */
    public function getCohortId()
    {
        return $this->cohortId;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Cohort
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set programYear
     *
     * @param \Ilios\CoreBundle\Entity\ProgramYear $programYear
     * @return Cohort
     */
    public function setProgramYear(\Ilios\CoreBundle\Entity\ProgramYear $programYear = null)
    {
        $this->programYear = $programYear;

        return $this;
    }

    /**
     * Get programYear
     *
     * @return \Ilios\CoreBundle\Entity\ProgramYear 
     */
    public function getProgramYear()
    {
        return $this->programYear;
    }

    /**
     * Add courses
     *
     * @param \Ilios\CoreBundle\Entity\Course $courses
     * @return Cohort
     */
    public function addCourse(\Ilios\CoreBundle\Entity\Course $courses)
    {
        $this->courses[] = $courses;

        return $this;
    }

    /**
     * Remove courses
     *
     * @param \Ilios\CoreBundle\Entity\Course $courses
     */
    public function removeCourse(\Ilios\CoreBundle\Entity\Course $courses)
    {
        $this->courses->removeElement($courses);
    }

    /**
     * Get courses
     *
     * @return \Ilios\CoreBundle\Entity\Course[]
     */
    public function getCourses()
    {
        return $this->courses->toArray();
    }
}
