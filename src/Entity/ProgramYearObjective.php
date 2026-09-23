<?php

declare(strict_types=1);

namespace App\Entity;

use App\Attributes as IA;
use App\Traits\ActivatableEntity;
use App\Traits\CategorizableEntity;
use App\Traits\IdentifiableEntity;
use App\Traits\MeshDescriptorsEntity;
use App\Traits\SortableEntity;
use App\Traits\StringableIdEntity;
use App\Traits\TitledEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\ProgramYearObjectiveRepository;

#[ORM\Table(name: 'program_year_x_objective')]
#[ORM\Index(columns: ['program_year_id'], name: 'IDX_7A16FDD6CB2B0673')]
#[ORM\Entity(repositoryClass: ProgramYearObjectiveRepository::class)]
#[IA\Entity]
class ProgramYearObjective implements ProgramYearObjectiveInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;
    use TitledEntity;
    use MeshDescriptorsEntity;
    use ActivatableEntity;
    use CategorizableEntity;
    use SortableEntity;

    #[ORM\Column(name: 'program_year_objective_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected int $id;

    #[ORM\ManyToOne(targetEntity: ProgramYear::class, inversedBy: 'programYearObjectives')]
    #[ORM\JoinColumn(name: 'program_year_id', referencedColumnName: 'program_year_id', onDelete: 'CASCADE')]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected ProgramYearInterface $programYear;

    #[ORM\Column(name: 'position', type: 'integer')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'integer')]
    protected int $position;

    #[ORM\ManyToMany(targetEntity: Term::class, inversedBy: 'programYearObjectives')]
    #[ORM\JoinTable(name: 'program_year_objective_x_term')]
    #[ORM\JoinColumn(
        name: 'program_year_objective_id',
        referencedColumnName: 'program_year_objective_id',
        onDelete: 'CASCADE'
    )]
    #[ORM\InverseJoinColumn(name: 'term_id', referencedColumnName: 'term_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $terms;

    #[ORM\Column(type: 'text')]
    #[IA\Expose]
    #[IA\Type('string')]
    #[IA\RemoveMarkup]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 65000)]
    protected string $title;

    #[ORM\ManyToOne(targetEntity: Competency::class, inversedBy: 'programYearObjectives')]
    #[ORM\JoinColumn(name: 'competency_id', referencedColumnName: 'competency_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected ?CompetencyInterface $competency = null;

    #[ORM\ManyToMany(targetEntity: CourseObjective::class, mappedBy: 'programYearObjectives')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $courseObjectives;

    #[ORM\ManyToMany(targetEntity: MeshDescriptor::class, inversedBy: 'programYearObjectives')]
    #[ORM\JoinTable(name: 'program_year_objective_x_mesh')]
    #[ORM\JoinColumn(
        name: 'program_year_objective_id',
        referencedColumnName: 'program_year_objective_id',
        onDelete: 'CASCADE'
    )]
    #[ORM\InverseJoinColumn(
        name: 'mesh_descriptor_uid',
        referencedColumnName: 'mesh_descriptor_uid',
        onDelete: 'CASCADE'
    )]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $meshDescriptors;

    #[ORM\ManyToOne(targetEntity: ProgramYearObjective::class, inversedBy: 'descendants')]
    #[ORM\JoinColumn(name: 'ancestor_id', referencedColumnName: 'program_year_objective_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected ?ProgramYearObjectiveInterface $ancestor = null;

    #[ORM\OneToMany(mappedBy: 'ancestor', targetEntity: ProgramYearObjective::class)]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $descendants;

    #[ORM\Column(type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\NotNull]
    #[Assert\Type(type: 'bool')]
    protected bool $active;

    public function __construct()
    {
        $this->position = 0;
        $this->active = true;
        $this->terms = new ArrayCollection();
        $this->courseObjectives = new ArrayCollection();
        $this->meshDescriptors = new ArrayCollection();
        $this->descendants = new ArrayCollection();
    }

    #[\Override]
    public function setProgramYear(ProgramYearInterface $programYear): void
    {
        $this->programYear = $programYear;
    }

    #[\Override]
    public function getProgramYear(): ProgramYearInterface
    {
        return $this->programYear;
    }

    #[\Override]
    public function setCompetency(?CompetencyInterface $competency = null): void
    {
        $this->competency = $competency;
    }

    #[\Override]
    public function getCompetency(): ?CompetencyInterface
    {
        return $this->competency;
    }

    #[\Override]
    public function setCourseObjectives(Collection $courseObjectives): void
    {
        $this->courseObjectives = new ArrayCollection();

        foreach ($courseObjectives as $courseObjective) {
            $this->addCourseObjective($courseObjective);
        }
    }

    #[\Override]
    public function addCourseObjective(CourseObjectiveInterface $courseObjective): void
    {
        if (!$this->courseObjectives->contains($courseObjective)) {
            $this->courseObjectives->add($courseObjective);
            $courseObjective->addProgramYearObjective($this);
        }
    }

    #[\Override]
    public function removeCourseObjective(CourseObjectiveInterface $courseObjective): void
    {
        if ($this->courseObjectives->contains($courseObjective)) {
            $this->courseObjectives->removeElement($courseObjective);
            $courseObjective->removeProgramYearObjective($this);
        }
    }

    #[\Override]
    public function getCourseObjectives(): Collection
    {
        return $this->courseObjectives;
    }

    #[\Override]
    public function setAncestor(?ProgramYearObjectiveInterface $ancestor = null): void
    {
        $this->ancestor = $ancestor;
    }

    #[\Override]
    public function getAncestor(): ?ProgramYearObjectiveInterface
    {
        return $this->ancestor;
    }

    #[\Override]
    public function getAncestorOrSelf(): ProgramYearObjectiveInterface
    {
        $ancestor = $this->getAncestor();

        return $ancestor ?: $this;
    }

    #[\Override]
    public function setDescendants(Collection $descendants): void
    {
        $this->descendants = new ArrayCollection();

        foreach ($descendants as $descendant) {
            $this->addDescendant($descendant);
        }
    }

    #[\Override]
    public function addDescendant(ProgramYearObjectiveInterface $descendant): void
    {
        if (!$this->descendants->contains($descendant)) {
            $this->descendants->add($descendant);
        }
    }

    #[\Override]
    public function removeDescendant(ProgramYearObjectiveInterface $descendant): void
    {
        $this->descendants->removeElement($descendant);
    }

    #[\Override]
    public function getDescendants(): Collection
    {
        return $this->descendants;
    }

    #[\Override]
    public function setMeshDescriptors(Collection $meshDescriptors): void
    {
        $this->meshDescriptors = new ArrayCollection();

        foreach ($meshDescriptors as $meshDescriptor) {
            $this->addMeshDescriptor($meshDescriptor);
        }
    }

    #[\Override]
    public function addMeshDescriptor(MeshDescriptorInterface $meshDescriptor): void
    {
        if (!$this->meshDescriptors->contains($meshDescriptor)) {
            $this->meshDescriptors->add($meshDescriptor);
        }
    }

    #[\Override]
    public function removeMeshDescriptor(MeshDescriptorInterface $meshDescriptor): void
    {
        $this->meshDescriptors->removeElement($meshDescriptor);
    }
}
