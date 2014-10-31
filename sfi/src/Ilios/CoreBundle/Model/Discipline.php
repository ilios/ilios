<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\TitledEntity;

/**
 * Class Discipline
 * @package Ilios\CoreBundle\Model
 */
class Discipline implements DisciplineInterface
{
    use IdentifiableEntity;
    use TitledEntity;

    /**
     * @var SchoolInterface
     */
    protected $owningSchool;

    /**
     * @var ArrayCollection|CourseInterface[]
     */
    protected $courses;

    /**
     * @var ArrayCollection|ProgramYearInterface[]
     */
    protected $programYears;

    /**
     * @var ArrayCollection|SessionInterface[]
     */
    protected $sessions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->courses = new ArrayCollection();
        $this->programYears = new ArrayCollection();
        $this->sessions = new ArrayCollection();
    }

    /**
     * @param SchoolInterface $school
     */
    public function setOwningSchool(SchoolInterface $school)
    {
        $this->owningSchool = $school;
    }

    /**
     * @return SchoolInterface
     */
    public function getOwningSchool()
    {
        return $this->owningSchool;
    }

    /**
     * @param CourseInterface $course
     */
    public function addCourse(CourseInterface $course)
    {
        $this->courses->add($course);
    }

    /**
     * @return ArrayCollection|CourseInterface[]
     */
    public function getCourses()
    {
        return $this->courses;
    }

    /**
     * @param Collection $programYears
     */
    public function setProgramYears(Collection $programYears)
    {
        $this->programYears = new ArrayCollection();

        foreach ($programYears as $programYear) {
            $this->addProgramYear($programYear);
        }
    }

    /**
     * @param ProgramYearInterface $programYear
     */
    public function addProgramYear(ProgramYearInterface $programYear)
    {
        $this->programYears->add($programYear);
    }

    /**
     * @return ArrayCollection|ProgramYearInterface[]
     */
    public function getProgramYears()
    {
        return $this->programYears;
    }

    /**
     * @param Collection $sessions
     */
    public function setSessions(Collection $sessions)
    {
        $this->sessions = new ArrayCollection();

        foreach ($sessions as $session) {
            $this->addSession($session);
        }
    }

    /**
     * @param SessionInterface $session
     */
    public function addSession(SessionInterface $session)
    {
        $this->sessions->add($session);
    }

    /**
     * @return ArrayCollection|SessionInterface[]
     */
    public function getSessions()
    {
        return $this->sessions;
    }
}
