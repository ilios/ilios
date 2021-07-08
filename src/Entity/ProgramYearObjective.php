<?php

declare(strict_types=1);

namespace App\Entity;

use App\Annotation as IS;
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
 *   indexes={
 *   }
 * )
 * @IS\Entity
 */
#[ORM\Table(name: 'program_year_x_objective')]
#[ORM\Index(name: 'IDX_7A16FDD6CB2B0673', columns: ['program_year_id'])]
#[ORM\Entity(repositoryClass: ProgramYearObjectiveRepository::class)]
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
     * @Assert\Type(type="integer")
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    #[ORM\Column(name: 'program_year_objective_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected $id;
    /**
     * @var ProgramYearInterface
     * @Assert\NotNull()
     * })
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\ManyToOne(targetEntity: 'ProgramYear', inversedBy: 'programYearObjectives')]
    #[ORM\JoinColumn(name: 'program_year_id', referencedColumnName: 'program_year_id', onDelete: 'CASCADE')]
    protected $programYear;
    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     * @IS\Expose
     * @IS\Type("integer")
     */
    #[ORM\Column(name: 'position', type: 'integer')]
    protected $position;
    /**
     * @var Collection
     *   joinColumns={
     *       name="program_year_objective_id", referencedColumnName="program_year_objective_id", onDelete="CASCADE"
     *     )
     *   },
     *   inverseJoinColumns={
     *   }
     * )
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'Term', inversedBy: 'programYearObjectives')]
    #[ORM\JoinTable(name: 'program_year_objective_x_term')]
    #[ORM\JoinColumn(name: 'program_year_objective_id', referencedColumnName: 'program_year_objective_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'term_id', referencedColumnName: 'term_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $terms;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 65000
     * )
     * @IS\Expose
     * @IS\Type("string")
     * @IS\RemoveMarkup
     */
    #[ORM\Column(type: 'text')]
    protected $title;
    /**
     * @var CompetencyInterface
     * })
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\ManyToOne(targetEntity: 'Competency', inversedBy: 'programYearObjectives')]
    #[ORM\JoinColumn(name: 'competency_id', referencedColumnName: 'competency_id')]
    protected $competency;
    /**
     * @var Collection
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'CourseObjective', mappedBy: 'programYearObjectives')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $courseObjectives;
    /**
     * @var Collection
     *   joinColumns={
     *      name="program_year_objective_id", referencedColumnName="program_year_objective_id", onDelete="CASCADE"
     *     )
     *   },
     *   inverseJoinColumns={
     *   }
     * )
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'MeshDescriptor', inversedBy: 'programYearObjectives')]
    #[ORM\JoinTable(name: 'program_year_objective_x_mesh')]
    #[ORM\JoinColumn(name: 'program_year_objective_id', referencedColumnName: 'program_year_objective_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'mesh_descriptor_uid', referencedColumnName: 'mesh_descriptor_uid', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $meshDescriptors;
    /**
     * @var ProgramYearObjectiveInterface
     * })
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\ManyToOne(targetEntity: 'ProgramYearObjective', inversedBy: 'descendants')]
    #[ORM\JoinColumn(name: 'ancestor_id', referencedColumnName: 'program_year_objective_id')]
    protected $ancestor;
    /**
     * @var Collection
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(targetEntity: 'ProgramYearObjective', mappedBy: 'ancestor')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $descendants;
    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @IS\Expose
     * @IS\Type("boolean")
     */
    #[ORM\Column(type: 'boolean')]
    protected $active;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->position = 0;
        $this->active = true;
        $this->terms = new ArrayCollection();
        $this->courseObjectives = new ArrayCollection();
        $this->meshDescriptors = new ArrayCollection();
        $this->descendants = new ArrayCollection();
    }
    /**
     * @inheritdoc
     */
    public function setProgramYear(ProgramYearInterface $programYear): void
    {
        $this->programYear = $programYear;
    }
    /**
     * @inheritdoc
     */
    public function getProgramYear(): ProgramYearInterface
    {
        return $this->programYear;
    }
    /**
     * @inheritdoc
     */
    public function setCompetency(CompetencyInterface $competency = null)
    {
        $this->competency = $competency;
    }
    /**
     * @inheritdoc
     */
    public function getCompetency()
    {
        return $this->competency;
    }
    /**
     * @inheritdoc
     */
    public function setCourseObjectives(Collection $courseObjectives)
    {
        $this->courseObjectives = new ArrayCollection();

        foreach ($courseObjectives as $courseObjective) {
            $this->addCourseObjective($courseObjective);
        }
    }
    /**
     * @inheritdoc
     */
    public function addCourseObjective(CourseObjectiveInterface $courseObjective)
    {
        if (!$this->courseObjectives->contains($courseObjective)) {
            $this->courseObjectives->add($courseObjective);
            $courseObjective->addProgramYearObjective($this);
        }
    }
    /**
     * @inheritdoc
     */
    public function removeCourseObjective(CourseObjectiveInterface $courseObjective)
    {
        if ($this->courseObjectives->contains($courseObjective)) {
            $this->courseObjectives->removeElement($courseObjective);
            $courseObjective->removeProgramYearObjective($this);
        }
    }
    /**
     * @inheritdoc
     */
    public function getCourseObjectives()
    {
        return $this->courseObjectives;
    }
    /**
     * @inheritdoc
     */
    public function setAncestor(ProgramYearObjectiveInterface $ancestor = null)
    {
        $this->ancestor = $ancestor;
    }
    /**
     * @inheritdoc
     */
    public function getAncestor()
    {
        return $this->ancestor;
    }
    /**
     * @inheritdoc
     */
    public function getAncestorOrSelf()
    {
        $ancestor = $this->getAncestor();

        return $ancestor ? $ancestor : $this;
    }
    /**
     * @inheritdoc
     */
    public function setDescendants(Collection $descendants)
    {
        $this->descendants = new ArrayCollection();

        foreach ($descendants as $descendant) {
            $this->addDescendant($descendant);
        }
    }
    /**
     * @inheritdoc
     */
    public function addDescendant(ProgramYearObjectiveInterface $descendant)
    {
        if (!$this->descendants->contains($descendant)) {
            $this->descendants->add($descendant);
        }
    }
    /**
     * @inheritdoc
     */
    public function removeDescendant(ProgramYearObjectiveInterface $descendant)
    {
        $this->descendants->removeElement($descendant);
    }
    /**
     * @inheritdoc
     */
    public function getDescendants()
    {
        return $this->descendants;
    }
    /**
     * @inheritdoc
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
    /**
     * @inheritdoc
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }
    /**
     * @inheritdoc
     */
    public function setActive($active)
    {
        $this->active = $active;
    }
    /**
     * @inheritdoc
     */
    public function setMeshDescriptors(Collection $meshDescriptors)
    {
        $this->meshDescriptors = new ArrayCollection();

        foreach ($meshDescriptors as $meshDescriptor) {
            $this->addMeshDescriptor($meshDescriptor);
        }
    }
    /**
     * @inheritdoc
     */
    public function addMeshDescriptor(MeshDescriptorInterface $meshDescriptor)
    {
        if (!$this->meshDescriptors->contains($meshDescriptor)) {
            $this->meshDescriptors->add($meshDescriptor);
        }
    }
    /**
     * @inheritdoc
     */
    public function removeMeshDescriptor(MeshDescriptorInterface $meshDescriptor)
    {
        $this->meshDescriptors->removeElement($meshDescriptor);
    }
}
