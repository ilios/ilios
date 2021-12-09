<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\LearnerGroupsEntity;
use App\Traits\UsersEntity;
use App\Attribute as IA;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\TitledEntity;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;
use App\Traits\CoursesEntity;
use App\Repository\CohortRepository;

/**
 * Class Cohort
 */
#[ORM\Entity(repositoryClass: CohortRepository::class)]
#[ORM\Table(name: 'cohort')]
#[ORM\Index(columns: ['program_year_id', 'cohort_id', 'title'], name: 'whole_k')]
#[IA\Entity]
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
     */
    #[ORM\Column(name: 'cohort_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\ReadOnly]
    protected $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 60
     * )
     */
    #[ORM\Column(type: 'string', length: 60)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $title;

    /**
     * @var ProgramYearInterface
     */
    #[ORM\OneToOne(targetEntity: 'ProgramYear', inversedBy: 'cohort')]
    #[ORM\JoinColumn(
        name: 'program_year_id',
        referencedColumnName: 'program_year_id',
        unique: true,
        onDelete: 'cascade'
    )]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected $programYear;

    /**
     * @var ArrayCollection|CourseInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'Course', mappedBy: 'cohorts')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $courses;

    /**
     * @var ArrayCollection|LearnerGroupInterface[]
     */
    #[ORM\OneToMany(mappedBy: 'cohort', targetEntity: 'LearnerGroup')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $learnerGroups;

    /**
     * @var Collection
     */
    #[ORM\ManyToMany(targetEntity: 'User', mappedBy: 'cohorts')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $users;

    public function __construct()
    {
        $this->courses = new ArrayCollection();
        $this->learnerGroups = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function setProgramYear(ProgramYearInterface $programYear = null)
    {
        $this->programYear = $programYear;
    }

    public function getProgramYear(): ?ProgramYearInterface
    {
        return $this->programYear;
    }

    public function addCourse(CourseInterface $course)
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
            $course->addCohort($this);
        }
    }

    public function removeCourse(CourseInterface $course)
    {
        if ($this->courses->contains($course)) {
            $this->courses->removeElement($course);
            $course->removeCohort($this);
        }
    }

    public function addUser(UserInterface $user)
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addCohort($this);
        }
    }

    public function removeUser(UserInterface $user)
    {
        $this->users->removeElement($user);
        $user->removeCohort($this);
    }

    public function getSchool(): ?SchoolInterface
    {
        if ($programYear = $this->getProgramYear()) {
            return $programYear->getSchool();
        }
        return null;
    }

    public function getProgram(): ?ProgramInterface
    {
        if ($programYear = $this->getProgramYear()) {
            return $programYear->getProgram();
        }
        return null;
    }
}
