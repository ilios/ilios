<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ilios\CoreBundle\Traits\LearnerGroupsEntity;
use Ilios\CoreBundle\Traits\UsersEntity;
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
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\CohortRepository")
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
    use LearnerGroupsEntity;
    use UsersEntity;

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
     * @ORM\OneToOne(targetEntity="ProgramYear", inversedBy="cohort")
     * @ORM\JoinColumn(name="program_year_id", referencedColumnName="program_year_id", unique=true, onDelete="cascade")
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("programYear")
     */
    protected $programYear;

    /**
    * @var ArrayCollection|CourseInterface[]
    *
    * @ORM\ManyToMany(targetEntity="Course", mappedBy="cohorts")
    *
    * @JMS\Expose
    * @JMS\Type("array<string>")
    */
    protected $courses;

    /**
     * @var ArrayCollection|LearnerGroupInterface[]
     *
     * @ORM\OneToMany(targetEntity="LearnerGroup", mappedBy="cohort")
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
        return $this->programYear;
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
     * @inheritdoc
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
    public function removeUser(UserInterface $user)
    {
        $this->users->removeElement($user);
        $user->removeCohort($this);
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
