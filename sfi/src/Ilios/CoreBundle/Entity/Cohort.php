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
use Ilios\CoreBundle\Traits\StringableIdEntity;

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
    use StringableIdEntity;

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
     * @var ArrayCollection|LearnerGroupInterface[]
     *
     * @ORM\OneToMany(targetEntity="LearnerGroup", mappedBy="cohort", fetch="EXTRA_LAZY")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("learnerGroups")
     */
    protected $learnerGroups;

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
    * @return LearnerGroupInterface[]|ArrayCollection
    */
    public function getLearnerGroups()
    {
        return $this->learnerGroups;
    }

    /**
    * @param Collection $learnerGroups
    */
    public function setLearnerGroups(Collection $learnerGroups)
    {
        $this->learnerGroups = new ArrayCollection();

        foreach ($learnerGroups as $group) {
            $this->addLearnerGroup($group);
        }
    }

    /**
    * @param LearnerGroupInterface $learnerGroup
    */
    public function addLearnerGroup(LearnerGroupInterface $learnerGroup)
    {
        $this->learnerGroups->add($learnerGroup);
    }
}
