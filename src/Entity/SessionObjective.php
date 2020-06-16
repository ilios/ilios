<?php

declare(strict_types=1);

namespace App\Entity;

use App\Annotation as IS;
use App\Traits\ActivatableEntity;
use App\Traits\CategorizableEntity;
use App\Traits\IdentifiableEntity;
use App\Traits\MeshDescriptorsEntity;
use App\Traits\SessionConsolidationEntity;
use App\Traits\SortableEntity;
use App\Traits\StringableIdEntity;
use App\Traits\TitledEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class SessionObjective
 *
 * @ORM\Table(name="session_x_objective",
 *   indexes={
 *     @ORM\Index(name="IDX_FA74B40B73484933", columns={"objective_id"}),
 *     @ORM\Index(name="IDX_FA74B40B613FECDF", columns={"session_id"})
 *   },
 *   uniqueConstraints={
 *     @ORM\UniqueConstraint(name="session_objective_uniq", columns={"session_id", "objective_id"})
 *  })
 * @ORM\Entity(repositoryClass="App\Entity\Repository\SessionObjectiveRepository")
 * @IS\Entity
 */
class SessionObjective implements SessionObjectiveInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;
    use SessionConsolidationEntity;
    use TitledEntity;
    use MeshDescriptorsEntity;
    use ActivatableEntity;
    use CategorizableEntity;
    use SortableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="session_objective_id", type="integer")
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
     * @var SessionInterface
     *
     * @Assert\NotNull()
     *
     * @ORM\ManyToOne(targetEntity="Session", inversedBy="sessionObjectives")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="session_id", referencedColumnName="session_id", onDelete="CASCADE")
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $session;

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
     * @ORM\ManyToOne(targetEntity="Objective", inversedBy="sessionObjectives")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="objective_id", referencedColumnName="objective_id", nullable=false)
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     * @IS\Expose
     */
    protected $objective;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Term", inversedBy="sessionObjectives")
     * @ORM\JoinTable(name="session_objective_x_term",
     *   joinColumns={
     *     @ORM\JoinColumn(name="session_objective_id", referencedColumnName="session_objective_id", onDelete="CASCADE")
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
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="CourseObjective", inversedBy="children")
     * @ORM\JoinTable("session_objective_x_course_objective",
     *   joinColumns={@ORM\JoinColumn(name="session_objective_id", referencedColumnName="session_objective_id")},
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="course_objective_id", referencedColumnName="course_objective_id")
     *   }
     * )
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $parents;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="MeshDescriptor", inversedBy="sessionObjectives")
     * @ORM\JoinTable(name="session_objective_x_mesh",
     *   joinColumns={
     *     @ORM\JoinColumn(name="session_objective_id", referencedColumnName="session_objective_id")
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
     * @var SessionObjectiveInterface
     *
     * @ORM\ManyToOne(targetEntity="SessionObjective", inversedBy="descendants")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ancestor_id", referencedColumnName="session_objective_id")
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $ancestor;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="SessionObjective", mappedBy="ancestor")
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
        $this->parents = new ArrayCollection();
        $this->meshDescriptors = new ArrayCollection();
        $this->descendants = new ArrayCollection();
    }

    /**
     * @inheritdoc
     */
    public function setSession(SessionInterface $session): void
    {
        $this->session = $session;
    }

    /**
     * @inheritdoc
     */
    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    /**
     * @inheritDoc
     */
    public function getIndexableCourses(): array
    {
        return [$this->session->getCourse()];
    }

    /**
     * @inheritdoc
     */
    public function setParents(Collection $parents)
    {
        $this->parents = new ArrayCollection();

        foreach ($parents as $parent) {
            $this->addParent($parent);
        }
    }

    /**
     * @inheritdoc
     */
    public function addParent(CourseObjectiveInterface $parent)
    {
        if (!$this->parents->contains($parent)) {
            $this->parents->add($parent);
            $this->getObjective()->addParent($parent->getObjective());
        }
    }

    /**
     * @inheritdoc
     */
    public function removeParent(CourseObjectiveInterface $parent)
    {
        $this->parents->removeElement($parent);
        $this->getObjective()->removeParent($parent->getObjective());
    }

    /**
     * @inheritdoc
     */
    public function getParents()
    {
        return $this->parents;
    }

    /**
     * @inheritdoc
     */
    public function setAncestor(SessionObjectiveInterface $ancestor = null)
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
    public function addDescendant(SessionObjectiveInterface $descendant)
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
    public function removeDescendant(SessionObjectiveInterface $descendant)
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
        if (! $this->objective) {
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
        $objective->addSessionObjective($this);
        $objective->setTitle($this->getTitle());
        $objective->setPosition($this->getPosition());
        $objective->setActive($this->isActive());
        $objective->setMeshDescriptors($this->getMeshDescriptors());
        $descendants = $this->getDescendants();
        /* @var SessionObjectiveInterface $descendant */
        foreach ($descendants as $descendant) {
            $objective->addDescendant($descendant->getObjective());
        }
        $ancestor = $this->getAncestor();
        if ($ancestor) {
            $objective->setAncestor($ancestor->getObjective());
        }
        $parents = $this->getParents();
        /* @var CourseObjectiveInterface $parent */
        foreach ($parents as $parent) {
            $objective->addParent($parent->getObjective());
        }
        return $objective;
    }
}
