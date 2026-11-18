<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\TitledNullableEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\StringableIdEntity;
use App\Attributes as IA;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\SchoolEntity;
use App\Repository\CompetencyRepository;
use Override;

#[ORM\Table(name: 'competency')]
#[ORM\Index(columns: ['parent_competency_id'], name: 'parent_competency_id_k')]
#[ORM\Entity(repositoryClass: CompetencyRepository::class)]
#[IA\Entity]
class Competency implements CompetencyInterface
{
    use TitledNullableEntity;
    use SchoolEntity;
    use StringableIdEntity;

    #[ORM\Column(name: 'competency_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected ?int $id;

    #[ORM\Column(type: 'string', length: 200, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 200)]
    protected ?string $title = null;

    #[ORM\ManyToOne(targetEntity: School::class, inversedBy: 'competencies')]
    #[ORM\JoinColumn(name: 'school_id', referencedColumnName: 'school_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected SchoolInterface $school;

    #[ORM\ManyToOne(targetEntity: Competency::class, inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'parent_competency_id', referencedColumnName: 'competency_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected ?CompetencyInterface $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: Competency::class)]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $children;

    #[ORM\ManyToMany(targetEntity: AamcPcrs::class, inversedBy: 'competencies')]
    #[ORM\JoinTable(name: 'competency_x_aamc_pcrs')]
    #[ORM\JoinColumn(name: 'competency_id', referencedColumnName: 'competency_id')]
    #[ORM\InverseJoinColumn(name: 'pcrs_id', referencedColumnName: 'pcrs_id')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $aamcPcrses;

    #[ORM\ManyToMany(targetEntity: ProgramYear::class, mappedBy: 'competencies')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $programYears;

    #[ORM\Column(type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\NotNull]
    #[Assert\Type(type: 'bool')]
    protected bool $active;

    #[ORM\OneToMany(mappedBy: 'competency', targetEntity: ProgramYearObjective::class)]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $programYearObjectives;

    public function __construct()
    {
        $this->aamcPcrses = new ArrayCollection();
        $this->programYears = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->programYearObjectives = new ArrayCollection();
        $this->active = true;
    }

    #[Override]
    public function setParent(?CompetencyInterface $parent = null): void
    {
        $this->parent = $parent;
    }

    #[Override]
    public function getParent(): ?CompetencyInterface
    {
        return $this->parent;
    }

    #[Override]
    public function setChildren(Collection $children): void
    {
        $this->children = new ArrayCollection();

        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    #[Override]
    public function addChild(CompetencyInterface $child): void
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
        }
    }

    #[Override]
    public function removeChild(CompetencyInterface $child): void
    {
        $this->children->removeElement($child);
        $child->setParent(null);
    }

    #[Override]
    public function getChildren(): Collection
    {
        return $this->children;
    }

    #[Override]
    public function hasChildren(): bool
    {
        return !$this->children->isEmpty();
    }

    #[Override]
    public function setAamcPcrses(Collection $aamcPcrses): void
    {
        $this->aamcPcrses = new ArrayCollection();

        foreach ($aamcPcrses as $aamcPcrs) {
            $this->addAamcPcrs($aamcPcrs);
        }
    }

    #[Override]
    public function addAamcPcrs(AamcPcrsInterface $aamcPcrs): void
    {
        if (!$this->aamcPcrses->contains($aamcPcrs)) {
            $this->aamcPcrses->add($aamcPcrs);
            $aamcPcrs->addCompetency($this);
        }
    }

    #[Override]
    public function removeAamcPcrs(AamcPcrsInterface $aamcPcrs): void
    {
        if ($this->aamcPcrses->contains($aamcPcrs)) {
            $this->aamcPcrses->removeElement($aamcPcrs);
            $aamcPcrs->removeCompetency($this);
        }
    }

    #[Override]
    public function getAamcPcrses(): Collection
    {
        return $this->aamcPcrses;
    }

    #[Override]
    public function addProgramYear(ProgramYearInterface $programYear): void
    {
        if (!$this->programYears->contains($programYear)) {
            $this->programYears->add($programYear);
            $programYear->addCompetency($this);
        }
    }

    #[Override]
    public function removeProgramYear(ProgramYearInterface $programYear): void
    {
        if ($this->programYears->contains($programYear)) {
            $this->programYears->removeElement($programYear);
            $programYear->removeCompetency($this);
        }
    }

    #[Override]
    public function setProgramYears(Collection $programYears): void
    {
        $this->programYears = new ArrayCollection();

        foreach ($programYears as $programYear) {
            $this->addProgramYear($programYear);
        }
    }
    #[Override]
    public function getProgramYears(): Collection
    {
        return $this->programYears;
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
    public function isActive(): bool
    {
        return $this->active;
    }

    #[Override]
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }


    #[Override]
    public function setProgramYearObjectives(?Collection $programYearObjectives = null): void
    {
        $this->programYearObjectives = new ArrayCollection();
        if (is_null($programYearObjectives)) {
            return;
        }

        foreach ($programYearObjectives as $programYearObjective) {
            $this->addProgramYearObjective($programYearObjective);
        }
    }

    #[Override]
    public function addProgramYearObjective(ProgramYearObjectiveInterface $programYearObjective): void
    {
        if (!$this->programYearObjectives->contains($programYearObjective)) {
            $this->programYearObjectives->add($programYearObjective);
        }
    }

    #[Override]
    public function removeProgramYearObjective(ProgramYearObjectiveInterface $programYearObjective): void
    {
        $this->programYearObjectives->removeElement($programYearObjective);
    }

    #[Override]
    public function getProgramYearObjectives(): Collection
    {
        return $this->programYearObjectives;
    }
}
