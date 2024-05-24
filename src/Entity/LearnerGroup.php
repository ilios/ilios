<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Traits\IlmSessionsEntity;
use App\Traits\InstructorGroupsEntity;
use App\Traits\InstructorsEntity;
use App\Traits\UsersEntity;
use App\Attributes as IA;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\TitledEntity;
use App\Traits\StringableIdEntity;
use App\Traits\OfferingsEntity;
use App\Repository\LearnerGroupRepository;

#[ORM\Table(name: '`group`')]
#[ORM\Entity(repositoryClass: LearnerGroupRepository::class)]
#[IA\Entity]
class LearnerGroup implements LearnerGroupInterface
{
    use IdentifiableEntity;
    use TitledEntity;
    use StringableIdEntity;
    use OfferingsEntity;
    use UsersEntity;
    use InstructorsEntity;
    use InstructorGroupsEntity;
    use IlmSessionsEntity;

    #[ORM\Column(name: 'group_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected int $id;

    #[ORM\Column(type: 'string', length: 60)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 60)]
    protected string $title;

    #[ORM\Column(name: 'location', type: 'string', length: 100, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 100)]
    protected ?string $location = null;

    #[ORM\Column(name: 'url', type: 'string', length: 2000, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 2000)]
    #[Assert\Url]
    protected ?string $url = null;

    #[ORM\Column(name: 'needs_accommodation', type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\NotNull]
    #[Assert\Type(type: 'bool')]
    protected bool $needsAccommodation;

    #[ORM\ManyToOne(targetEntity: 'Cohort', inversedBy: 'learnerGroups')]
    #[ORM\JoinColumn(name: 'cohort_id', referencedColumnName: 'cohort_id', nullable: false, onDelete: 'CASCADE')]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected CohortInterface $cohort;

    #[ORM\ManyToOne(targetEntity: 'LearnerGroup', inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'parent_group_id', referencedColumnName: 'group_id', onDelete: 'CASCADE')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected ?LearnerGroupInterface $parent = null;

    #[ORM\ManyToOne(targetEntity: 'LearnerGroup', inversedBy: 'descendants')]
    #[ORM\JoinColumn(name: 'ancestor_id', referencedColumnName: 'group_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected ?LearnerGroupInterface $ancestor = null;

    #[ORM\OneToMany(mappedBy: 'ancestor', targetEntity: 'LearnerGroup')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $descendants;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: 'LearnerGroup')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $children;

    #[ORM\ManyToMany(targetEntity: 'IlmSession', mappedBy: 'learnerGroups')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $ilmSessions;

    #[ORM\ManyToMany(targetEntity: 'Offering', mappedBy: 'learnerGroups')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $offerings;

    #[ORM\ManyToMany(targetEntity: 'InstructorGroup', inversedBy: 'learnerGroups')]
    #[ORM\JoinTable(name: 'group_x_instructor_group')]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'group_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(
        name: 'instructor_group_id',
        referencedColumnName: 'instructor_group_id',
        onDelete: 'CASCADE'
    )]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $instructorGroups;

    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'learnerGroups')]
    #[ORM\JoinTable(name: 'group_x_user')]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'group_id')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $users;

    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'instructedLearnerGroups')]
    #[ORM\JoinTable(name: 'group_x_instructor')]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'group_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $instructors;

    public function __construct()
    {
        $this->users            = new ArrayCollection();
        $this->ilmSessions      = new ArrayCollection();
        $this->offerings        = new ArrayCollection();
        $this->children         = new ArrayCollection();
        $this->instructorGroups = new ArrayCollection();
        $this->instructors      = new ArrayCollection();
        $this->descendants      = new ArrayCollection();
        $this->needsAccommodation = false;
    }

    public function setLocation(?string $location): void
    {
        $this->location = $location;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setCohort(CohortInterface $cohort): void
    {
        $this->cohort = $cohort;
    }

    public function getCohort(): CohortInterface
    {
        return $this->cohort;
    }

    public function addIlmSession(IlmSessionInterface $ilmSession): void
    {
        if (!$this->ilmSessions->contains($ilmSession)) {
            $this->ilmSessions->add($ilmSession);
            $ilmSession->addLearnerGroup($this);
        }
    }

    public function removeIlmSession(IlmSessionInterface $ilmSession): void
    {
        if ($this->ilmSessions->contains($ilmSession)) {
            $this->ilmSessions->removeElement($ilmSession);
            $ilmSession->removeLearnerGroup($this);
        }
    }

    public function setParent(?LearnerGroupInterface $parent = null): void
    {
        $this->parent = $parent;
    }

    public function getParent(): ?LearnerGroupInterface
    {
        return $this->parent;
    }

    public function setAncestor(?LearnerGroupInterface $ancestor = null): void
    {
        $this->ancestor = $ancestor;
    }

    public function getAncestor(): ?LearnerGroupInterface
    {
        return $this->ancestor;
    }

    /**
     * If the group has no ancestor then we need the material itself
     */
    public function getAncestorOrSelf(): LearnerGroupInterface
    {
        $ancestor = $this->getAncestor();

        return $ancestor ?: $this;
    }

    public function setDescendants(Collection $descendants): void
    {
        $this->descendants = new ArrayCollection();

        foreach ($descendants as $descendant) {
            $this->addDescendant($descendant);
        }
    }

    public function addDescendant(LearnerGroupInterface $descendant): void
    {
        if (!$this->descendants->contains($descendant)) {
            $this->descendants->add($descendant);
            $descendant->setAncestor($this);
        }
    }

    public function removeDescendant(LearnerGroupInterface $descendant): void
    {
        $this->descendants->removeElement($descendant);
    }

    public function getDescendants(): Collection
    {
        return $this->descendants;
    }

    public function setChildren(?Collection $children = null): void
    {
        $this->children = new ArrayCollection();
        if (is_null($children)) {
            return;
        }

        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    public function addChild(LearnerGroupInterface $child): void
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
        }
    }

    public function removeChild(LearnerGroupInterface $child): void
    {
        $this->children->removeElement($child);
    }

    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addOffering(OfferingInterface $offering): void
    {
        if (!$this->offerings->contains($offering)) {
            $this->offerings->add($offering);
            $offering->addLearnerGroup($this);
        }
    }

    public function removeOffering(OfferingInterface $offering): void
    {
        if ($this->offerings->contains($offering)) {
            $this->offerings->removeElement($offering);
            $offering->removeLearnerGroup($this);
        }
    }

    public function getSchool(): ?SchoolInterface
    {
        return $this->cohort->getSchool();
    }

    public function getProgram(): ?ProgramInterface
    {
        return $this->cohort->getProgram();
    }

    public function getProgramYear(): ?ProgramYearInterface
    {
        return $this->getCohort()->getProgramYear();
    }

    public function setNeedsAccommodation(bool $needsAccommodation): void
    {
        $this->needsAccommodation = $needsAccommodation;
    }

    public function getNeedsAccommodation(): bool
    {
        return $this->needsAccommodation;
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }
}
