<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\SessionConsolidationEntity;
use App\Attributes as IA;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\StringableIdEntity;
use DateTime;
use App\Repository\IlmSessionRepository;
use Override;

#[ORM\Table(name: 'ilm_session_facet')]
#[ORM\Entity(repositoryClass: IlmSessionRepository::class)]
#[IA\Entity]
class IlmSession implements IlmSessionInterface
{
    use StringableIdEntity;
    use SessionConsolidationEntity;

    #[ORM\Column(name: 'ilm_session_facet_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected ?int $id;

    #[ORM\OneToOne(inversedBy: 'ilmSession', targetEntity: Session::class)]
    #[ORM\JoinColumn(
        name: 'session_id',
        referencedColumnName: 'session_id',
        unique: true,
        nullable: false,
        onDelete: 'CASCADE'
    )]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotBlank]
    protected SessionInterface $session;

    #[ORM\Column(name: 'hours', type: 'decimal', precision: 6, scale: 2)]
    #[IA\Expose]
    #[IA\Type('float')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'numeric')]
    #[Assert\Length(min: 0, max: 10000)]
    protected string|float $hours;

    #[ORM\Column(name: 'due_date', type: 'datetime')]
    #[IA\Expose]
    #[IA\Type('dateTime')]
    #[Assert\NotBlank]
    protected DateTime $dueDate;

    #[ORM\ManyToMany(targetEntity: LearnerGroup::class, inversedBy: 'ilmSessions')]
    #[ORM\JoinTable(name: 'ilm_session_facet_x_group')]
    #[ORM\JoinColumn(name: 'ilm_session_facet_id', referencedColumnName: 'ilm_session_facet_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'group_id', referencedColumnName: 'group_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $learnerGroups;

    #[ORM\ManyToMany(targetEntity: InstructorGroup::class, inversedBy: 'ilmSessions')]
    #[ORM\JoinTable(name: 'ilm_session_facet_x_instructor_group')]
    #[ORM\JoinColumn(name: 'ilm_session_facet_id', referencedColumnName: 'ilm_session_facet_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(
        name: 'instructor_group_id',
        referencedColumnName: 'instructor_group_id',
        onDelete: 'CASCADE'
    )]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $instructorGroups;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'instructorIlmSessions')]
    #[ORM\JoinTable(name: 'ilm_session_facet_x_instructor')]
    #[ORM\JoinColumn(name: 'ilm_session_facet_id', referencedColumnName: 'ilm_session_facet_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $instructors;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'learnerIlmSessions')]
    #[ORM\JoinTable(name: 'ilm_session_facet_x_learner')]
    #[ORM\JoinColumn(name: 'ilm_session_facet_id', referencedColumnName: 'ilm_session_facet_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $learners;

    public function __construct()
    {
        $this->learnerGroups = new ArrayCollection();
        $this->instructors = new ArrayCollection();
        $this->instructorGroups = new ArrayCollection();
        $this->learners = new ArrayCollection();
    }

    #[Override]
    public function setHours(float $hours): void
    {
        $this->hours = $hours;
    }

    #[Override]
    public function getHours(): float
    {
        //we have to type cast float because doctrine returns it as a string for precision
        return (float) $this->hours;
    }

    #[Override]
    public function setDueDate(?DateTime $dueDate = null): void
    {
        $this->dueDate = $dueDate;
    }

    #[Override]
    public function getDueDate(): DateTime
    {
        return $this->dueDate;
    }

    #[Override]
    public function getAllInstructors(): Collection
    {
        $instructors = $this->getInstructors()->toArray();
        foreach ($this->getInstructorGroups() as $group) {
            $instructors = array_merge($instructors, $group->getUsers()->toArray());
        }

        return new ArrayCollection($instructors);
    }

    #[Override]
    public function setSession(SessionInterface $session): void
    {
        $this->session = $session;
    }

    #[Override]
    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    #[Override]
    public function getSchool(): ?SchoolInterface
    {
        return $this->session->getCourse()->getSchool();
    }
    #[Override]
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    #[Override]
    public function getId(): ?int
    {
        return $this->id;
    }

    #[Override]
    public function setInstructorGroups(Collection $instructorGroups): void
    {
        $this->instructorGroups = new ArrayCollection();

        foreach ($instructorGroups as $instructorGroup) {
            $this->addInstructorGroup($instructorGroup);
        }
    }

    #[Override]
    public function addInstructorGroup(InstructorGroupInterface $instructorGroup): void
    {
        if (!$this->instructorGroups->contains($instructorGroup)) {
            $this->instructorGroups->add($instructorGroup);
        }
    }

    #[Override]
    public function removeInstructorGroup(InstructorGroupInterface $instructorGroup): void
    {
        $this->instructorGroups->removeElement($instructorGroup);
    }

    #[Override]
    public function getInstructorGroups(): Collection
    {
        return $this->instructorGroups;
    }

    #[Override]
    public function setInstructors(Collection $instructors): void
    {
        $this->instructors = new ArrayCollection();

        foreach ($instructors as $instructor) {
            $this->addInstructor($instructor);
        }
    }

    #[Override]
    public function addInstructor(UserInterface $instructor): void
    {
        if (!$this->instructors->contains($instructor)) {
            $this->instructors->add($instructor);
        }
    }

    #[Override]
    public function removeInstructor(UserInterface $instructor): void
    {
        $this->instructors->removeElement($instructor);
    }

    #[Override]
    public function getInstructors(): Collection
    {
        return $this->instructors;
    }

    #[Override]
    public function setLearnerGroups(Collection $learnerGroups): void
    {
        $this->learnerGroups = new ArrayCollection();

        foreach ($learnerGroups as $learnerGroup) {
            $this->addLearnerGroup($learnerGroup);
        }
    }

    #[Override]
    public function addLearnerGroup(LearnerGroupInterface $learnerGroup): void
    {
        if (!$this->learnerGroups->contains($learnerGroup)) {
            $this->learnerGroups->add($learnerGroup);
        }
    }

    #[Override]
    public function removeLearnerGroup(LearnerGroupInterface $learnerGroup): void
    {
        $this->learnerGroups->removeElement($learnerGroup);
    }

    #[Override]
    public function getLearnerGroups(): Collection
    {
        return $this->learnerGroups;
    }


    #[Override]
    public function setLearners(Collection $learners): void
    {
        $this->learners = new ArrayCollection();

        foreach ($learners as $learner) {
            $this->addLearner($learner);
        }
    }

    #[Override]
    public function addLearner(UserInterface $learner): void
    {
        if (!$this->learners->contains($learner)) {
            $this->learners->add($learner);
        }
    }

    #[Override]
    public function removeLearner(UserInterface $learner): void
    {
        $this->learners->removeElement($learner);
    }

    #[Override]
    public function getLearners(): Collection
    {
        return $this->learners;
    }
}
