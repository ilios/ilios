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
 *   indexes={
 *   }
 * )
 * @IS\Entity
 */
#[ORM\Table(name: 'course_x_objective')]
#[ORM\Index(name: 'IDX_3B37B1AD591CC992', columns: ['course_id'])]
#[ORM\Entity(repositoryClass: CourseObjectiveRepository::class)]
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
     * @Assert\Type(type="integer")
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    #[ORM\Column(name: 'course_objective_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected $id;
    /**
     * @var CourseInterface
     * @Assert\NotNull()
     * })
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\ManyToOne(targetEntity: 'Course', inversedBy: 'courseObjectives')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'course_id', onDelete: 'CASCADE')]
    protected $course;
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
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'Term', inversedBy: 'courseObjectives')]
    #[ORM\JoinTable(name: 'course_objective_x_term')]
    #[ORM\JoinColumn(name: 'course_objective_id', referencedColumnName: 'course_objective_id', onDelete: 'CASCADE')]
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
     * @var Collection
     *   joinColumns={
     *   },
     *   inverseJoinColumns={
     *       name="program_year_objective_id", referencedColumnName="program_year_objective_id", onDelete="CASCADE"
     *     )
     *   },
     * )
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'ProgramYearObjective', inversedBy: 'courseObjectives')]
    #[ORM\JoinTable('course_objective_x_program_year_objective')]
    #[ORM\JoinColumn(name: 'course_objective_id', referencedColumnName: 'course_objective_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(
        name: 'program_year_objective_id',
        referencedColumnName: 'program_year_objective_id',
        onDelete: 'CASCADE'
    )]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $programYearObjectives;
    /**
     * @var Collection
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'SessionObjective', mappedBy: 'courseObjectives')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $sessionObjectives;
    /**
     * @var Collection
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'MeshDescriptor', inversedBy: 'courseObjectives')]
    #[ORM\JoinTable(name: 'course_objective_x_mesh')]
    #[ORM\JoinColumn(name: 'course_objective_id', referencedColumnName: 'course_objective_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'mesh_descriptor_uid', referencedColumnName: 'mesh_descriptor_uid', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $meshDescriptors;
    /**
     * @var CourseObjectiveInterface
     * })
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\ManyToOne(targetEntity: 'CourseObjective', inversedBy: 'descendants')]
    #[ORM\JoinColumn(name: 'ancestor_id', referencedColumnName: 'course_objective_id')]
    protected $ancestor;
    /**
     * @var Collection
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(targetEntity: 'CourseObjective', mappedBy: 'ancestor')]
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
        $this->programYearObjectives = new ArrayCollection();
        $this->sessionObjectives = new ArrayCollection();
        $this->meshDescriptors = new ArrayCollection();
        $this->descendants = new ArrayCollection();
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
        }
    }
    /**
     * @inheritdoc
     */
    public function removeProgramYearObjective(ProgramYearObjectiveInterface $programYearObjective)
    {
        $this->programYearObjectives->removeElement($programYearObjective);
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
        }
    }
    /**
     * @inheritdoc
     */
    public function removeDescendant(CourseObjectiveInterface $descendant)
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
