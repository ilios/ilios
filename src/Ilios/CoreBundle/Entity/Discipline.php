<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\TitledEntity;

/**
 * Class Discipline
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="discipline")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessType("public_method")
 */
class Discipline implements DisciplineInterface
{
    use IdentifiableEntity;
    use TitledEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="discipline_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     * @JMS\SerializedName("id")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=200, nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 200
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $title;

    /**
     * @var SchoolInterface
     *
     * @ORM\ManyToOne(targetEntity="School", inversedBy="disciplines")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="owning_school_id", referencedColumnName="school_id")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("owningSchool")
     */
    protected $owningSchool;

    /**
     * @var ArrayCollection|CourseInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Course", mappedBy="disciplines")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $courses;

    /**
     * @var ArrayCollection|ProgramYearInterface[]
     *
     * @ORM\ManyToMany(targetEntity="ProgramYear", mappedBy="disciplines")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("programYears")
     */
    protected $programYears;

    /**
     * @var ArrayCollection|SessionInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Session", mappedBy="disciplines")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
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
        if ($this->owningSchool && !$this->owningSchool->isDeleted()) {
            return $this->owningSchool;
        }
        
        return null;
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
     * @return ArrayCollection|CourseInterface[]
     */
    public function getCourses()
    {
        return $this->courses->filter(function ($entity) {
            return !$entity->isDeleted();
        });
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
        return $this->programYears->filter(function ($entity) {
            return !$entity->isDeleted();
        });
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
        return $this->sessions->filter(function ($entity) {
            return !$entity->isDeleted();
        });
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->id;
    }
}
