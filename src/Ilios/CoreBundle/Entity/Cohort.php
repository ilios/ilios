<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\TitledEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;
use Ilios\CoreBundle\Traits\CoursesEntity;

/**
 * Class Cohort
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(
 *  name="cohort",
 *  indexes={
 *      @ORM\Index(name="whole_k", columns={"program_year_id", "cohort_id", "title"})
 *  }
 * )
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessType("public_method")
 */
class Cohort implements CohortInterface
{
    use IdentifiableEntity;
    use TitledEntity;
    use StringableIdEntity;
    use CoursesEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="cohort_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
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
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 60
     * )
     *
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
    * @var Collection
    *
    * @ORM\ManyToMany(targetEntity="User", mappedBy="cohorts")
    *
    * @JMS\Expose
    * @JMS\Type("array<string>")
    */
    protected $users;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->courses = new ArrayCollection();
        $this->learnerGroups = new ArrayCollection();
        $this->users = new ArrayCollection();
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
        if ($this->programYear && !$this->programYear->isDeleted()) {
            return $this->programYear;
        }
        
        return null;
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

    /**
     * @param Collection $users
     */
    public function setUsers(Collection $users = null)
    {
        $this->users = new ArrayCollection();
        if (is_null($users)) {
            return;
        }

        foreach ($users as $user) {
            $this->addUser($user);
        }
    }

    /**
     * @param UserInterface $user
     */
    public function addUser(UserInterface $user)
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addCohort($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function addCourse(CourseInterface $course)
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
            $course->addCohort($this);
        }
    }

    /**
     * @return ArrayCollection|UserInterface[]
     */
    public function getUsers()
    {
        return $this->users;
    }
    
    /**
     * Check if a cohorts program year is deleted
     * @return boolean
     */
    public function isDeleted()
    {
        return is_null($this->getProgramYear());
    }

    /**
     * @inheritdoc
     */
    public function getSchool()
    {
        if ($programYear = $this->getProgramYear()) {
            return $programYear->getSchool();
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getProgram()
    {
        if ($programYear = $this->getProgramYear()) {
            return $programYear->getProgram();
        }
        return null;
    }
}
