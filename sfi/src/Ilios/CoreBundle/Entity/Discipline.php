<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Discipline
 */
class Discipline
{
    /**
     * @var integer
     */
    private $disciplineId;

    /**
     * @var string
     */
    private $title;
    
    /**
     * @var \Ilios\CoreBundle\Entity\School
     */
    private $owningSchool;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $courses;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $programYears;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $sessions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->courses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->programYears = new \Doctrine\Common\Collections\ArrayCollection();
        $this->sessions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get disciplineId
     *
     * @return integer 
     */
    public function getDisciplineId()
    {
        return $this->disciplineId;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Discipline
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
     * Set owningSchool
     *
     * @param \Ilios\CoreBundle\Entity\School $school
     * @return ProgramYearSteward
     */
    public function setOwningSchool(\Ilios\CoreBundle\Entity\School $school = null)
    {
        $this->owningSchool = $school;

        return $this;
    }

    /**
     * Get owningSchool
     *
     * @return \Ilios\CoreBundle\Entity\School 
     */
    public function getOwningSchool()
    {
        return $this->owningSchool;
    }

    /**
     * Add courses
     *
     * @param \Ilios\CoreBundle\Entity\Course $courses
     * @return Discipline
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

    /**
     * Add programYears
     *
     * @param \Ilios\CoreBundle\Entity\ProgramYear $programYears
     * @return Discipline
     */
    public function addProgramYear(\Ilios\CoreBundle\Entity\ProgramYear $programYears)
    {
        $this->programYears[] = $programYears;

        return $this;
    }

    /**
     * Remove programYears
     *
     * @param \Ilios\CoreBundle\Entity\ProgramYear $programYears
     */
    public function removeProgramYear(\Ilios\CoreBundle\Entity\ProgramYear $programYears)
    {
        $this->programYears->removeElement($programYears);
    }

    /**
     * Get programYears
     *
     * @return \Ilios\CoreBundle\Entity\ProgramYear[]
     */
    public function getProgramYears()
    {
        return $this->programYears->toArray();
    }

    /**
     * Add sessions
     *
     * @param \Ilios\CoreBundle\Entity\Session $sessions
     * @return Discipline
     */
    public function addSession(\Ilios\CoreBundle\Entity\Session $sessions)
    {
        $this->sessions[] = $sessions;

        return $this;
    }

    /**
     * Remove sessions
     *
     * @param \Ilios\CoreBundle\Entity\Session $sessions
     */
    public function removeSession(\Ilios\CoreBundle\Entity\Session $sessions)
    {
        $this->sessions->removeElement($sessions);
    }

    /**
     * Get sessions
     *
     * @return \Ilios\CoreBundle\Entity\Session[]
     */
    public function getSessions()
    {
        return $this->sessions->toArray();
    }
}
