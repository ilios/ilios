<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Attributes as IA;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\InstructorGroupRepository;
use Override;

#[ORM\Table(name: 'instructor_group')]
#[ORM\Entity(repositoryClass: InstructorGroupRepository::class)]
#[IA\Entity]
class InstructorGroup implements InstructorGroupInterface
{
    #[ORM\Column(name: 'instructor_group_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected ?int $id;

    #[ORM\Column(type: 'string', length: 60)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 60)]
    protected string $title;

    #[ORM\ManyToOne(targetEntity: School::class, inversedBy: 'instructorGroups')]
    #[ORM\JoinColumn(name: 'school_id', referencedColumnName: 'school_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected SchoolInterface $school;

    #[ORM\ManyToMany(targetEntity: LearnerGroup::class, mappedBy: 'instructorGroups')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $learnerGroups;

    #[ORM\ManyToMany(targetEntity: IlmSession::class, mappedBy: 'instructorGroups')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $ilmSessions;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'instructorGroups')]
    #[ORM\JoinTable(name: 'instructor_group_x_user')]
    #[ORM\JoinColumn(name: 'instructor_group_id', referencedColumnName: 'instructor_group_id')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $users;

    #[ORM\ManyToMany(targetEntity: Offering::class, mappedBy: 'instructorGroups')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $offerings;

    public function __construct()
    {
        $this->learnerGroups = new ArrayCollection();
        $this->ilmSessions = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->offerings = new ArrayCollection();
    }

    #[Override]
    public function addLearnerGroup(LearnerGroupInterface $learnerGroup): void
    {
        if (!$this->learnerGroups->contains($learnerGroup)) {
            $this->learnerGroups->add($learnerGroup);
            $learnerGroup->addInstructorGroup($this);
        }
    }

    #[Override]
    public function removeLearnerGroup(LearnerGroupInterface $learnerGroup): void
    {
        if ($this->learnerGroups->contains($learnerGroup)) {
            $this->learnerGroups->removeElement($learnerGroup);
            $learnerGroup->removeInstructorGroup($this);
        }
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
    public function getLearnerGroups(): Collection
    {
        return $this->learnerGroups;
    }

    #[Override]
    public function addIlmSession(IlmSessionInterface $ilmSession): void
    {
        if (!$this->ilmSessions->contains($ilmSession)) {
            $this->ilmSessions->add($ilmSession);
            $ilmSession->addInstructorGroup($this);
        }
    }

    #[Override]
    public function removeIlmSession(IlmSessionInterface $ilmSession): void
    {
        if ($this->ilmSessions->contains($ilmSession)) {
            $this->ilmSessions->removeElement($ilmSession);
            $ilmSession->removeInstructorGroup($this);
        }
    }

    #[Override]
    public function setIlmSessions(Collection $ilmSessions): void
    {
        $this->ilmSessions = new ArrayCollection();

        foreach ($ilmSessions as $ilmSession) {
            $this->addIlmSession($ilmSession);
        }
    }

    #[Override]
    public function getIlmSessions(): Collection
    {
        return $this->ilmSessions;
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
    public function setOfferings(Collection $offerings): void
    {
        $this->offerings = new ArrayCollection();

        foreach ($offerings as $offering) {
            $this->addOffering($offering);
        }
    }

    #[Override]
    public function addOffering(OfferingInterface $offering): void
    {
        if (!$this->offerings->contains($offering)) {
            $this->offerings->add($offering);
        }
    }

    #[Override]
    public function removeOffering(OfferingInterface $offering): void
    {
        $this->offerings->removeElement($offering);
    }

    #[Override]
    public function getOfferings(): Collection
    {
        return $this->offerings;
    }

    #[Override]
    public function getSchool(): SchoolInterface
    {
        return $this->school;
    }

    #[Override]
    public function setSchool(SchoolInterface $school): void
    {
        $this->school = $school;
    }

    #[Override]
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    #[Override]
    public function getTitle(): string
    {
        return $this->title;
    }

    #[Override]
    public function __toString(): string
    {
        return isset($this->id) ? (string) $this->id : '';
    }

    #[Override]
    public function setUsers(Collection $users): void
    {
        $this->users = new ArrayCollection();

        foreach ($users as $user) {
            $this->addUser($user);
        }
    }

    #[Override]
    public function addUser(UserInterface $user): void
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }
    }

    #[Override]
    public function removeUser(UserInterface $user): void
    {
        $this->users->removeElement($user);
    }

    #[Override]
    public function getUsers(): Collection
    {
        return $this->users;
    }
}
