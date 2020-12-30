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
use App\Repository\CourseObjectiveRepository;

/**
 * Class CourseObjective
 *
 * @ORM\Table(name="course_x_objective",
 *   indexes={
 *     @ORM\Index(name="IDX_3B37B1AD73484933", columns={"objective_id"}),
 *     @ORM\Index(name="IDX_3B37B1AD591CC992", columns={"course_id"})
 *   },
 *   uniqueConstraints={
 *     @ORM\UniqueConstraint(name="course_objective_uniq", columns={"course_id", "objective_id"})
 *  })
 * @ORM\Entity(repositoryClass=CourseObjectiveRepository::class)
 * @IS\Entity
 */
class CourseObjective implements CourseObjectiveInterface
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
     * @ORM\Column(name="course_objective_id", type="integer")
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
     * @var CourseInterface
     *
     * @Assert\NotNull()
     *
     * @ORM\ManyToOne(targetEntity="Course", inversedBy="courseObjectives")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="course_id", referencedColumnName="course_id", onDelete="CASCADE")
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $course;

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
     * @ORM\ManyToOne(targetEntity="Objective", inversedBy="courseObjectives", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="objective_id", referencedColumnName="objective_id", nullable=false)
     * })
     *
     * @IS\Type("entity")
     * @IS\ReadOnly()
     */
    protected $objective;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Term", inversedBy="courseObjectives")
     * @ORM\JoinTable(name="course_objective_x_term",
     *   joinColumns={
     *     @ORM\JoinColumn(name="course_objective_id", referencedColumnName="course_objective_id", onDelete="CASCADE")
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
     * @ORM\ManyToMany(targetEntity="ProgramYearObjective", inversedBy="courseObjectives")
     * @ORM\JoinTable("course_objective_x_program_year_objective",
     *   joinColumns={
     *     @ORM\JoinColumn(name="course_objective_id", referencedColumnName="course_objective_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(
     *       name="program_year_objective_id", referencedColumnName="program_year_objective_id", onDelete="CASCADE"
     *     )
     *   },
     * )
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $programYearObjectives;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="SessionObjective", mappedBy="courseObjectives")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $sessionObjectives;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="MeshDescriptor", inversedBy="courseObjectives")
     * @ORM\JoinTable(name="course_objective_x_mesh",
     *   joinColumns={
     *     @ORM\JoinColumn(name="course_objective_id", referencedColumnName="course_objective_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="mesh_descriptor_uid", referencedColumnName="mesh_descriptor_uid", onDelete="CASCADE")
     *   }
     * )
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $meshDescriptors;

    /**
     * @var CourseObjectiveInterface
     *
     * @ORM\ManyToOne(targetEntity="CourseObjective", inversedBy="descendants")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ancestor_id", referencedColumnName="course_objective_id")
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $ancestor;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="CourseObjective", mappedBy="ancestor")
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
        $this->position = 0;
        $this->active = true;
        $this->terms = new ArrayCollection();
        $this->programYearObjectives = new ArrayCollection();
        $this->sessionObjectives = new ArrayCollection();
        $this->meshDescriptors = new ArrayCollection();
        $this->descendants = new ArrayCollection();
        $this->objective = new Objective();
    }

    /**
     * @inheritdoc
     */
    public function setCourse(CourseInterface $course): void
    {
        $this->course = $course;
    }

    /**
     * @inheritdoc
     */
    public function getCourse(): CourseInterface
    {
        return $this->course;
    }

    /**
     * @inheritDoc
     */
    public function getIndexableCourses(): array
    {
        return [$this->getCourse()];
    }

    /**
     * @inheritdoc
     */
    public function setProgramYearObjectives(Collection $programYearObjectives)
    {
        $this->programYearObjectives = new ArrayCollection();

        foreach ($programYearObjectives as $programYearObjective) {
            $this->addProgramYearObjective($programYearObjective);
        }
    }

    /**
     * @inheritdoc
     */
    public function addProgramYearObjective(ProgramYearObjectiveInterface $programYearObjective)
    {
        if (!$this->programYearObjectives->contains($programYearObjective)) {
            $this->programYearObjectives->add($programYearObjective);
            $this->getObjective()->addParent($programYearObjective->getObjective());
        }
    }

    /**
     * @inheritdoc
     */
    public function removeProgramYearObjective(ProgramYearObjectiveInterface $programYearObjective)
    {
        $this->programYearObjectives->removeElement($programYearObjective);
        $this->getObjective()->removeParent($programYearObjective->getObjective());
    }

    /**
     * @inheritdoc
     */
    public function getProgramYearObjectives()
    {
        return $this->programYearObjectives;
    }

    /**
     * @inheritdoc
     */
    public function setSessionObjectives(Collection $sessionObjectives)
    {
        $this->sessionObjectives = new ArrayCollection();

        foreach ($sessionObjectives as $sessionObjective) {
            $this->addSessionObjective($sessionObjective);
        }
    }

    /**
     * @inheritdoc
     */
    public function addSessionObjective(SessionObjectiveInterface $sessionObjective)
    {
        if (!$this->sessionObjectives->contains($sessionObjective)) {
            $this->sessionObjectives->add($sessionObjective);
            $sessionObjective->addCourseObjective($this);
            $this->getObjective()->addChild($sessionObjective->getObjective());
        }
    }

    /**
     * @inheritdoc
     */
    public function removeSessionObjective(SessionObjectiveInterface $sessionObjective)
    {
        if ($this->sessionObjectives->contains($sessionObjective)) {
            $this->sessionObjectives->removeElement($sessionObjective);
            $sessionObjective->removeCourseObjective($this);
            $this->getObjective()->removeChild($sessionObjective->getObjective());
        }
    }

    /**
     * @inheritdoc
     */
    public function getSessionObjectives()
    {
        return $this->sessionObjectives;
    }

    /**
     * @inheritdoc
     */
    public function setAncestor(CourseObjectiveInterface $ancestor = null)
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
    public function addDescendant(CourseObjectiveInterface $descendant)
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
    public function removeDescendant(CourseObjectiveInterface $descendant)
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
}
