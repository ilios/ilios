<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Ilios\CoreBundle\Entity\CourseInterface;
use Ilios\CoreBundle\Entity\ProgramYearInterface;
use Ilios\CoreBundle\Traits\TitledEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;

/**
 * Class Cohort
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(
 *  name="cohort",
 * 	indexes={
 * 	    @ORM\Index(name="whole_k", columns={"program_year_id", "cohort_id", "title"})
 * 	}
 * )
 *
 * @JMS\ExclusionPolicy("all")
 */
class Cohort implements CohortInterface
{
    use IdentifiableEntity;
    use TitledEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="cohort_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=60)
     * @todo should be on the TitledEntity Trait
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     *
     * @Assert\Type(type="string", message="type.not_valid")
     */
    protected $title;

    /**
     * @var ProgramYearInterface
     *
     * @ORM\OneToOne(targetEntity="ProgramYear", fetch="EXTRA_LAZY", inversedBy="cohort")
     * @ORM\JoinColumn(name="program_year_id", referencedColumnName="program_year_id", unique=true)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("programYear")
     */
    protected $programYear;

    /**
    * @var ArrayCollection|CourseInterface[]
    *
    * @ORM\ManyToMany(targetEntity="Course", mappedBy="cohorts", fetch="EXTRA_LAZY")
    *
    * @JMS\Expose
    * @JMS\Type("array<string>")
    */
    protected $courses;

    /**
     * @var ArrayCollection|GroupInterface[]
     *
     * @ORM\OneToMany(targetEntity="Group", mappedBy="cohort", fetch="EXTRA_LAZY")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("learnerGroups")
     * @todo: alt-type: <Ilios\CoreBundle\Entity\Group>
     */
    protected $groups;

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

    /**
    * @return GroupInterface[]|ArrayCollection
    */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
    * @param Collection $groups
    */
    public function setGroups(Collection $groups)
    {
        $this->groups = new ArrayCollection();

        foreach ($groups as $group) {
            $this->addGroup($group);
        }
    }

    /**
    * @param GroupInterface $group
    */
    public function addGroup(GroupInterface $group)
    {
        $this->groups->add($group);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->id;
    }
}
