<?php

declare(strict_types=1);

namespace App\Entity;

use App\Attribute as IA;
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

/**
 * Class ProgramYearObjective
 */
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

    /**
     * @var int
     */
    #[ORM\Column(name: 'program_year_objective_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected $id;

    /**
     * @var ProgramYearInterface
     */
    #[ORM\ManyToOne(targetEntity: 'ProgramYear', inversedBy: 'programYearObjectives')]
    #[ORM\JoinColumn(name: 'program_year_id', referencedColumnName: 'program_year_id', onDelete: 'CASCADE')]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected $programYear;

    /**
     * @var int
     */
    #[ORM\Column(name: 'position', type: 'integer')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'integer')]
    protected $position;

    /**
     * @var Collection
     */
    #[ORM\ManyToMany(targetEntity: 'Term', inversedBy: 'programYearObjectives')]
    #[ORM\JoinTable(name: 'program_year_objective_x_term')]
    #[ORM\JoinColumn(
        name: 'program_year_objective_id',
        referencedColumnName: 'program_year_objective_id',
        onDelete: 'CASCADE'
    )]
    #[ORM\InverseJoinColumn(name: 'term_id', referencedColumnName: 'term_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $terms;

    /**
     * @var string
     */
    #[ORM\Column(type: 'text')]
    #[IA\Expose]
    #[IA\Type('string')]
    #[IA\RemoveMarkup]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 65000)]
    protected $title;

    /**
     * @var CompetencyInterface
     */
    #[ORM\ManyToOne(targetEntity: 'Competency', inversedBy: 'programYearObjectives')]
    #[ORM\JoinColumn(name: 'competency_id', referencedColumnName: 'competency_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected $competency;

    /**
     * @var Collection
     */
    #[ORM\ManyToMany(targetEntity: 'CourseObjective', mappedBy: 'programYearObjectives')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $courseObjectives;

    /**
     * @var Collection
     */
    #[ORM\ManyToMany(targetEntity: 'MeshDescriptor', inversedBy: 'programYearObjectives')]
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
    #[IA\Type('entityCollection')]
    protected $meshDescriptors;

    /**
     * @var ProgramYearObjectiveInterface
     */
    #[ORM\ManyToOne(targetEntity: 'ProgramYearObjective', inversedBy: 'descendants')]
    #[ORM\JoinColumn(name: 'ancestor_id', referencedColumnName: 'program_year_objective_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected $ancestor;

    /**
     * @var Collection
     */
    #[ORM\OneToMany(targetEntity: 'ProgramYearObjective', mappedBy: 'ancestor')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $descendants;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\NotNull]
    #[Assert\Type(type: 'bool')]
    protected $active;

    public function __construct()
    {
        $this->position = 0;
        $this->active = true;
        $this->terms = new ArrayCollection();
        $this->courseObjectives = new ArrayCollection();
        $this->meshDescriptors = new ArrayCollection();
        $this->descendants = new ArrayCollection();
    }

    public function setProgramYear(ProgramYearInterface $programYear): void
    {
        $this->programYear = $programYear;
    }

    public function getProgramYear(): ProgramYearInterface
    {
        return $this->programYear;
    }

    public function setCompetency(CompetencyInterface $competency = null)
    {
        $this->competency = $competency;
    }

    public function getCompetency(): ?CompetencyInterface
    {
        return $this->competency;
    }

    public function setCourseObjectives(Collection $courseObjectives)
    {
        $this->courseObjectives = new ArrayCollection();

        foreach ($courseObjectives as $courseObjective) {
            $this->addCourseObjective($courseObjective);
        }
    }

    public function addCourseObjective(CourseObjectiveInterface $courseObjective)
    {
        if (!$this->courseObjectives->contains($courseObjective)) {
            $this->courseObjectives->add($courseObjective);
            $courseObjective->addProgramYearObjective($this);
        }
    }

    public function removeCourseObjective(CourseObjectiveInterface $courseObjective)
    {
        if ($this->courseObjectives->contains($courseObjective)) {
            $this->courseObjectives->removeElement($courseObjective);
            $courseObjective->removeProgramYearObjective($this);
        }
    }

    public function getCourseObjectives(): Collection
    {
        return $this->courseObjectives;
    }

    public function setAncestor(ProgramYearObjectiveInterface $ancestor = null)
    {
        $this->ancestor = $ancestor;
    }

    public function getAncestor(): ?ProgramYearObjectiveInterface
    {
        return $this->ancestor;
    }

    public function getAncestorOrSelf(): ProgramYearObjectiveInterface
    {
        $ancestor = $this->getAncestor();

        return $ancestor ? $ancestor : $this;
    }

    public function setDescendants(Collection $descendants)
    {
        $this->descendants = new ArrayCollection();

        foreach ($descendants as $descendant) {
            $this->addDescendant($descendant);
        }
    }

    public function addDescendant(ProgramYearObjectiveInterface $descendant)
    {
        if (!$this->descendants->contains($descendant)) {
            $this->descendants->add($descendant);
        }
    }

    public function removeDescendant(ProgramYearObjectiveInterface $descendant)
    {
        $this->descendants->removeElement($descendant);
    }

    public function getDescendants(): Collection
    {
        return $this->descendants;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setPosition($position)
    {
        $this->position = $position;
    }

    public function setActive($active)
    {
        $this->active = $active;
    }

    public function setMeshDescriptors(Collection $meshDescriptors)
    {
        $this->meshDescriptors = new ArrayCollection();

        foreach ($meshDescriptors as $meshDescriptor) {
            $this->addMeshDescriptor($meshDescriptor);
        }
    }

    public function addMeshDescriptor(MeshDescriptorInterface $meshDescriptor)
    {
        if (!$this->meshDescriptors->contains($meshDescriptor)) {
            $this->meshDescriptors->add($meshDescriptor);
        }
    }

    public function removeMeshDescriptor(MeshDescriptorInterface $meshDescriptor)
    {
        $this->meshDescriptors->removeElement($meshDescriptor);
    }
}
