<?php

declare(strict_types=1);

namespace App\Entity;

use App\Attributes as IA;
use App\Repository\OfferingRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'offering')]
#[ORM\Index(columns: ['session_id'], name: 'session_id_k')]
#[ORM\Index(columns: ['offering_id', 'session_id', 'start_date', 'end_date'], name: 'offering_dates_session_k')]
#[ORM\Entity(repositoryClass: OfferingRepository::class)]
#[IA\Entity]
class Offering implements OfferingInterface
{
    #[ORM\Column(name: 'offering_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected ?int $id;

    #[ORM\Column(name: 'room', type: 'string', length: 255, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 255)]
    protected ?string $room = null;

    #[ORM\Column(name: 'site', type: 'string', length: 255, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 255)]
    protected ?string $site = null;

    #[ORM\Column(name: 'url', type: 'string', length: 2000, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 2000)]
    #[Assert\Url(requireTld: false)]
    protected ?string $url = null;

    #[ORM\Column(name: 'start_date', type: 'datetime')]
    #[IA\Expose]
    #[IA\Type('dateTime')]
    #[Assert\NotBlank]
    protected DateTime $startDate;

    #[ORM\Column(name: 'end_date', type: 'datetime')]
    #[IA\Expose]
    #[IA\Type('dateTime')]
    #[Assert\NotBlank]
    protected DateTime $endDate;

    #[ORM\Column(name: 'last_updated_on', type: 'datetime')]
    #[IA\Expose]
    #[IA\OnlyReadable]
    #[IA\Type('dateTime')]
    #[Assert\NotBlank]
    protected DateTime $updatedAt;

    #[ORM\ManyToOne(targetEntity: Session::class, inversedBy: 'offerings')]
    #[ORM\JoinColumn(name: 'session_id', referencedColumnName: 'session_id', onDelete: 'CASCADE')]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected SessionInterface $session;

    #[ORM\ManyToMany(targetEntity: LearnerGroup::class, inversedBy: 'offerings')]
    #[ORM\JoinTable(name: 'offering_x_group')]
    #[ORM\JoinColumn(name: 'offering_id', referencedColumnName: 'offering_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'group_id', referencedColumnName: 'group_id')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $learnerGroups;

    #[ORM\ManyToMany(targetEntity: InstructorGroup::class, inversedBy: 'offerings')]
    #[ORM\JoinTable(name: 'offering_x_instructor_group')]
    #[ORM\JoinColumn(name: 'offering_id', referencedColumnName: 'offering_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'instructor_group_id', referencedColumnName: 'instructor_group_id')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $instructorGroups;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'offerings')]
    #[ORM\JoinTable(name: 'offering_x_learner')]
    #[ORM\JoinColumn(name: 'offering_id', referencedColumnName: 'offering_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $learners;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'instructedOfferings')]
    #[ORM\JoinTable(name: 'offering_x_instructor')]
    #[ORM\JoinColumn(name: 'offering_id', referencedColumnName: 'offering_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $instructors;

    public function __construct()
    {
        $this->updatedAt = new DateTime();
        $this->learnerGroups = new ArrayCollection();
        $this->instructorGroups = new ArrayCollection();
        $this->learners = new ArrayCollection();
        $this->instructors = new ArrayCollection();
    }

    #[Override]
    public function setRoom(?string $room): void
    {
        $this->room = $room;
    }

    #[Override]
    public function getRoom(): ?string
    {
        return $this->room;
    }

    #[Override]
    public function setSite(?string $site): void
    {
        $this->site = $site;
    }

    #[Override]
    public function getSite(): ?string
    {
        return $this->site;
    }

    #[Override]
    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    #[Override]
    public function getUrl(): ?string
    {
        return $this->url;
    }

    #[Override]
    public function setStartDate(?DateTime $startDate = null): void
    {
        $this->startDate = $startDate;
    }

    #[Override]
    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    #[Override]
    public function setEndDate(?DateTime $endDate = null): void
    {
        $this->endDate = $endDate;
    }

    #[Override]
    public function getEndDate(): DateTime
    {
        return $this->endDate;
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
    public function getAllInstructors(): Collection
    {
        $instructors = $this->getInstructors()->toArray();
        foreach ($this->getInstructorGroups() as $group) {
            $instructors = array_merge($instructors, $group->getUsers()->toArray());
        }

        return new ArrayCollection($instructors);
    }

    #[Override]
    public function getAlertProperties(): array
    {
        $instructorIds = $this->getInstructors()->map(fn(UserInterface $entity) => $entity->getId())->toArray();
        sort($instructorIds);
        $instructorGroupIds = $this->getInstructorGroups()->map(
            fn(InstructorGroupInterface $entity) => $entity->getId()
        )->toArray();
        sort($instructorGroupIds);
        $learnerIds = $this->getLearners()->map(fn(UserInterface $entity) => $entity->getId())->toArray();
        sort($learnerIds);
        $learnerGroupIds = $this->getLearnerGroups()->map(
            fn(LearnerGroupInterface $entity) => $entity->getId()
        )->toArray();
        sort($learnerGroupIds);
        $room = $this->getRoom();
        $site = $this->getSite();
        $url = $this->getUrl();
        $startDate = $this->getStartDate()->getTimestamp();
        $endDate = $this->getEndDate()->getTimestamp();

        return [
            'instructors' => $instructorIds,
            'instructorGroups' => $instructorGroupIds,
            'learners' => $learnerIds,
            'learnerGroups' => $learnerGroupIds,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'room' => $room,
            'site' => $site,
            'url' => $url,
        ];
    }

    #[Override]
    public function getSchool(): SchoolInterface
    {
        return $this->session->getSchool();
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

    #[Override]
    public function getSessions(): Collection
    {
        $session = $this->getSession();
        return new ArrayCollection([$session]);
    }

    #[Override]
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    #[Override]
    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    #[Override]
    public function getClassName(): string
    {
        return __CLASS__;
    }

    #[Override]
    public function __toString(): string
    {
        return isset($this->id) ? (string) $this->id : '';
    }
}
