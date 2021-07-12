<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\LearnerGroupsEntity;
use App\Traits\UsersEntity;
use App\Annotation as IS;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\TitledEntity;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;
use App\Traits\CoursesEntity;
use App\Repository\CohortRepository;

/**
 * Class Cohort
 * @IS\Entity
 */
#[ORM\Entity(repositoryClass: CohortRepository::class)]
#[ORM\Table(name: 'cohort')]
#[ORM\Index(columns: ['program_year_id', 'cohort_id', 'title'], name: 'whole_k')]
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
     * @Assert\Type(type="integer")
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    #[ORM\Column(name: 'cohort_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected $id;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 60
     * )
     */
    #[ORM\Column(type: 'string', length: 60)]
    protected $title;

    /**
     * @var ProgramYearInterface
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\OneToOne(targetEntity: 'ProgramYear', inversedBy: 'cohort')]
    #[ORM\JoinColumn(
        name: 'program_year_id',
        referencedColumnName: 'program_year_id',
        unique: true,
        onDelete: 'cascade'
    )]
    protected $programYear;

    /**
     * @var ArrayCollection|CourseInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'Course', mappedBy: 'cohorts')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $courses;

    /**
     * @var ArrayCollection|LearnerGroupInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(mappedBy: 'cohort', targetEntity: 'LearnerGroup')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $learnerGroups;

    /**
     * @var Collection
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'User', mappedBy: 'cohorts')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $users;

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
    public function removeCourse(CourseInterface $course)
    {
        if ($this->courses->contains($course)) {
            $this->courses->removeElement($course);
            $course->removeCohort($this);
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
