<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\ProgramYearObjectivesEntity;
use App\Traits\TitledNullableEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\ActivatableEntity;
use App\Traits\StringableIdEntity;
use App\Attributes as IA;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\ProgramYearsEntity;
use App\Traits\SchoolEntity;
use App\Repository\CompetencyRepository;

#[ORM\Table(name: 'competency')]
#[ORM\Index(columns: ['parent_competency_id'], name: 'parent_competency_id_k')]
#[ORM\Entity(repositoryClass: CompetencyRepository::class)]
#[IA\Entity]
class Competency implements CompetencyInterface
{
    use IdentifiableEntity;
    use TitledNullableEntity;
    use ProgramYearsEntity;
    use SchoolEntity;
    use StringableIdEntity;
    use ActivatableEntity;
    use ProgramYearObjectivesEntity;

    #[ORM\Column(name: 'competency_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected int $id;

    #[ORM\Column(type: 'string', length: 200, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 200)]
    protected ?string $title = null;

    #[ORM\ManyToOne(targetEntity: 'School', inversedBy: 'competencies')]
    #[ORM\JoinColumn(name: 'school_id', referencedColumnName: 'school_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected SchoolInterface $school;

    #[ORM\ManyToOne(targetEntity: 'Competency', inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'parent_competency_id', referencedColumnName: 'competency_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected ?CompetencyInterface $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: 'Competency')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $children;

    #[ORM\ManyToMany(targetEntity: 'AamcPcrs', inversedBy: 'competencies')]
    #[ORM\JoinTable(name: 'competency_x_aamc_pcrs')]
    #[ORM\JoinColumn(name: 'competency_id', referencedColumnName: 'competency_id')]
    #[ORM\InverseJoinColumn(name: 'pcrs_id', referencedColumnName: 'pcrs_id')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $aamcPcrses;

    #[ORM\ManyToMany(targetEntity: 'ProgramYear', mappedBy: 'competencies')]
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

    #[ORM\OneToMany(mappedBy: 'competency', targetEntity: 'ProgramYearObjective')]
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

    public function setParent(?CompetencyInterface $parent = null): void
    {
        $this->parent = $parent;
    }

    public function getParent(): ?CompetencyInterface
    {
        return $this->parent;
    }

    public function setChildren(Collection $children): void
    {
        $this->children = new ArrayCollection();

        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    public function addChild(CompetencyInterface $child): void
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
        }
    }

    public function removeChild(CompetencyInterface $child): void
    {
        $this->children->removeElement($child);
        $child->setParent(null);
    }

    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function hasChildren(): bool
    {
        return !$this->children->isEmpty();
    }

    public function setAamcPcrses(Collection $aamcPcrses): void
    {
        $this->aamcPcrses = new ArrayCollection();

        foreach ($aamcPcrses as $aamcPcrs) {
            $this->addAamcPcrs($aamcPcrs);
        }
    }

    public function addAamcPcrs(AamcPcrsInterface $aamcPcrs): void
    {
        if (!$this->aamcPcrses->contains($aamcPcrs)) {
            $this->aamcPcrses->add($aamcPcrs);
            $aamcPcrs->addCompetency($this);
        }
    }

    public function removeAamcPcrs(AamcPcrsInterface $aamcPcrs): void
    {
        if ($this->aamcPcrses->contains($aamcPcrs)) {
            $this->aamcPcrses->removeElement($aamcPcrs);
            $aamcPcrs->removeCompetency($this);
        }
    }

    public function getAamcPcrses(): Collection
    {
        return $this->aamcPcrses;
    }

    public function addProgramYear(ProgramYearInterface $programYear): void
    {
        if (!$this->programYears->contains($programYear)) {
            $this->programYears->add($programYear);
            $programYear->addCompetency($this);
        }
    }

    public function removeProgramYear(ProgramYearInterface $programYear): void
    {
        if ($this->programYears->contains($programYear)) {
            $this->programYears->removeElement($programYear);
            $programYear->removeCompetency($this);
        }
    }
}
