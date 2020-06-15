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

/**
 * Class ProgramYearObjective
 *
 * @ORM\Table(name="program_year_x_objective",
 *   indexes={
 *     @ORM\Index(name="IDX_7A16FDD673484933", columns={"objective_id"}),
 *     @ORM\Index(name="IDX_7A16FDD6CB2B0673", columns={"program_year_id"})
 *   },
 *   uniqueConstraints={
 *     @ORM\UniqueConstraint(name="program_year_objective_uniq", columns={"program_year_id", "objective_id"})
 *  })
 * @ORM\Entity(repositoryClass="App\Entity\Repository\ProgramYearObjectiveRepository")
 * @IS\Entity
 */
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
     *
     * @ORM\Column(name="program_year_objective_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     *
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    protected $id;

    /**
     * @var ProgramYearInterface
     *
     * @Assert\NotNull()
     *
     * @ORM\ManyToOne(targetEntity="ProgramYear", inversedBy="programYearObjectives")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="program_year_id", referencedColumnName="program_year_id", onDelete="CASCADE")
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $programYear;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     *
     * @ORM\Column(name="position", type="integer")
     *
     * @IS\Expose
     * @IS\Type("integer")
     */
    protected $position;

    /**
     * @var ObjectiveInterface
     *
     * @Assert\NotNull()
     *
     * @ORM\ManyToOne(targetEntity="Objective", inversedBy="programYearObjectives")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="objective_id", referencedColumnName="objective_id", nullable=false)
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     * @IS\ReadOnly()
     */
    protected $objective;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Term", inversedBy="programYearObjectives")
     * @ORM\JoinTable(name="program_year_objective_x_term",
     *   joinColumns={
     *     @ORM\JoinColumn(
     *       name="program_year_objective_id", referencedColumnName="program_year_objective_id", onDelete="CASCADE"
     *     )
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="term_id", referencedColumnName="term_id", onDelete="CASCADE")
     *   }
     * )
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $terms;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 65000
     * )
     *
     * @IS\Expose
     * @IS\Type("string")
     * @IS\RemoveMarkup
     */
    protected $title;

    /**
     * @var CompetencyInterface
     *
     * @ORM\ManyToOne(targetEntity="Competency", inversedBy="programYearObjectives")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="competency_id", referencedColumnName="competency_id")
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $competency;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="CourseObjective", mappedBy="parents")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $children;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="MeshDescriptor", inversedBy="programYearObjectives")
     * @ORM\JoinTable(name="program_year_objective_x_mesh",
     *   joinColumns={
     *     @ORM\JoinColumn(name="program_year_objective_id", referencedColumnName="program_year_objective_id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="mesh_descriptor_uid", referencedColumnName="mesh_descriptor_uid")
     *   }
     * )
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $meshDescriptors;

    /**
     * @var ProgramYearObjectiveInterface
     *
     * @ORM\ManyToOne(targetEntity="ProgramYearObjective", inversedBy="descendants")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ancestor_id", referencedColumnName="program_year_objective_id")
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $ancestor;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="ProgramYearObjective", mappedBy="ancestor")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $descendants;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     *
     * @IS\Expose
     * @IS\Type("boolean")
     */
    protected $active;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->terms = new ArrayCollection();
        $this->children = new ArrayCollection();
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
        $this->getObjective()->setCompetency($competency);
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
    public function setChildren(Collection $children)
    {
        $this->children = new ArrayCollection();

        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    /**
     * @inheritdoc
     */
    public function addChild(CourseObjectiveInterface $child)
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->addParent($this);
            $this->getObjective()->addChild($child->getObjective());
        }
    }

    /**
     * @inheritdoc
     */
    public function removeChild(CourseObjectiveInterface $child)
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            $child->removeParent($this);
            $this->getObjective()->removeChild($child->getObjective());
        }
    }

    /**
     * @inheritdoc
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @inheritdoc
     */
    public function setAncestor(ProgramYearObjectiveInterface $ancestor = null)
    {
        $this->ancestor = $ancestor;
        $this->getObjective()->setAncestor($ancestor->getObjective());
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
            $objective = $descendant->getObjective();
            $this->getObjective()->addDescendant($objective);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeDescendant(ProgramYearObjectiveInterface $descendant)
    {
        $this->descendants->removeElement($descendant);
        $objective = $descendant->getObjective();
        $this->getObjective()->removeDescendant($objective);
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
    public function setObjective(ObjectiveInterface $objective): void
    {
        $this->objective = $objective;
    }

    /**
     * @inheritdoc
     */
    public function getObjective(): ObjectiveInterface
    {
        if ($this->objective) {
            $this->objective = $this->createObjectiveFromThis();
        }
        return $this->objective;
    }

    /**
     * @inheritdoc
     */
    public function setTitle($title)
    {
        $this->title = $title;
        $this->getObjective()->setTitle($title);
    }

    /**
     * @inheritdoc
     */
    public function setPosition($position)
    {
        $this->position = $position;
        $this->getObjective()->setPosition($position);
    }

    /**
     * @inheritdoc
     */
    public function setActive($active)
    {
        $this->active = $active;
        $this->getObjective()->setActive($active);
    }

    /**
     * @inheritdoc
     */
    public function setMeshDescriptors(Collection $meshDescriptors)
    {
        $this->meshDescriptors = new ArrayCollection();

        foreach ($meshDescriptors as $meshDescriptor) {
            $this->addMeshDescriptor($meshDescriptor);
            $this->getObjective()->addMeshDescriptor($meshDescriptor);
        }
    }

    /**
     * @inheritdoc
     */
    public function addMeshDescriptor(MeshDescriptorInterface $meshDescriptor)
    {
        if (!$this->meshDescriptors->contains($meshDescriptor)) {
            $this->meshDescriptors->add($meshDescriptor);
            $this->getObjective()->addMeshDescriptor($meshDescriptor);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeMeshDescriptor(MeshDescriptorInterface $meshDescriptor)
    {
        $this->meshDescriptors->removeElement($meshDescriptor);
        $this->getObjective()->removeMeshDescriptor($meshDescriptor);
    }

    /**
     * @return ObjectiveInterface
     */
    protected function createObjectiveFromThis(): ObjectiveInterface
    {
        $objective = new Objective();
        $objective->addProgramYearObjective($this);
        $objective->setTitle($this->getTitle());
        $objective->setPosition($this->getPosition());
        $objective->setActive($this->isActive());
        $objective->setCompetency($this->getCompetency());
        $objective->setMeshDescriptors($this->getMeshDescriptors());
        $descendants = $this->getDescendants();
        /* @var CourseObjectiveInterface $descendant */
        foreach ($descendants as $descendant) {
            $objective->addDescendant($descendant->getObjective());
        }
        $ancestor = $this->getAncestor();
        if ($ancestor) {
            $objective->setAncestor($ancestor->getObjective());
        }

        $children = $this->getChildren();
        /* @var ProgramYearObjectiveInterface $parent */
        foreach ($children as $child) {
            $objective->addChild($child->getObjective());
        }
        return $objective;
    }
}
