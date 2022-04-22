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
use App\Repository\CourseObjectiveRepository;

/**
 * Class CourseObjective
 */
#[ORM\Table(name: 'course_x_objective')]
#[ORM\Index(columns: ['course_id'], name: 'IDX_3B37B1AD591CC992')]
#[ORM\Entity(repositoryClass: CourseObjectiveRepository::class)]
#[IA\Entity]
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
     */
    #[ORM\Column(name: 'course_objective_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    protected $id;

    /**
     * @var CourseInterface
     * @Assert\NotNull()
     */
    #[ORM\ManyToOne(targetEntity: 'Course', inversedBy: 'courseObjectives')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'course_id', onDelete: 'CASCADE')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected $course;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     */
    #[ORM\Column(name: 'position', type: 'integer')]
    #[IA\Expose]
    #[IA\Type('integer')]
    protected $position;

    /**
     * @var Collection
     */
    #[ORM\ManyToMany(targetEntity: 'Term', inversedBy: 'courseObjectives')]
    #[ORM\JoinTable(name: 'course_objective_x_term')]
    #[ORM\JoinColumn(name: 'course_objective_id', referencedColumnName: 'course_objective_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'term_id', referencedColumnName: 'term_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $terms;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 65000
     * )
     */
    #[ORM\Column(type: 'text')]
    #[IA\Expose]
    #[IA\Type('string')]
    #[IA\RemoveMarkup]
    protected $title;

    /**
     * @var Collection
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
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $programYearObjectives;

    /**
     * @var Collection
     */
    #[ORM\ManyToMany(targetEntity: 'SessionObjective', mappedBy: 'courseObjectives')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $sessionObjectives;

    /**
     * @var Collection
     */
    #[ORM\ManyToMany(targetEntity: 'MeshDescriptor', inversedBy: 'courseObjectives')]
    #[ORM\JoinTable(name: 'course_objective_x_mesh')]
    #[ORM\JoinColumn(name: 'course_objective_id', referencedColumnName: 'course_objective_id', onDelete: 'CASCADE')]
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
     * @var CourseObjectiveInterface
     */
    #[ORM\ManyToOne(targetEntity: 'CourseObjective', inversedBy: 'descendants')]
    #[ORM\JoinColumn(name: 'ancestor_id', referencedColumnName: 'course_objective_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected $ancestor;

    /**
     * @var Collection
     */
    #[ORM\OneToMany(mappedBy: 'ancestor', targetEntity: 'CourseObjective')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $descendants;

    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     */
    #[ORM\Column(type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    protected $active;

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

    public function setCourse(CourseInterface $course): void
    {
        $this->course = $course;
    }

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

    public function setProgramYearObjectives(Collection $programYearObjectives)
    {
        $this->programYearObjectives = new ArrayCollection();

        foreach ($programYearObjectives as $programYearObjective) {
            $this->addProgramYearObjective($programYearObjective);
        }
    }

    public function addProgramYearObjective(ProgramYearObjectiveInterface $programYearObjective)
    {
        if (!$this->programYearObjectives->contains($programYearObjective)) {
            $this->programYearObjectives->add($programYearObjective);
        }
    }

    public function removeProgramYearObjective(ProgramYearObjectiveInterface $programYearObjective)
    {
        $this->programYearObjectives->removeElement($programYearObjective);
    }

    public function getProgramYearObjectives(): Collection
    {
        return $this->programYearObjectives;
    }

    public function setSessionObjectives(Collection $sessionObjectives)
    {
        $this->sessionObjectives = new ArrayCollection();

        foreach ($sessionObjectives as $sessionObjective) {
            $this->addSessionObjective($sessionObjective);
        }
    }

    public function addSessionObjective(SessionObjectiveInterface $sessionObjective)
    {
        if (!$this->sessionObjectives->contains($sessionObjective)) {
            $this->sessionObjectives->add($sessionObjective);
            $sessionObjective->addCourseObjective($this);
        }
    }

    public function removeSessionObjective(SessionObjectiveInterface $sessionObjective)
    {
        if ($this->sessionObjectives->contains($sessionObjective)) {
            $this->sessionObjectives->removeElement($sessionObjective);
            $sessionObjective->removeCourseObjective($this);
        }
    }

    public function getSessionObjectives(): Collection
    {
        return $this->sessionObjectives;
    }

    public function setAncestor(CourseObjectiveInterface $ancestor = null)
    {
        $this->ancestor = $ancestor;
    }

    public function getAncestor(): ?CourseObjectiveInterface
    {
        return $this->ancestor;
    }

    public function getAncestorOrSelf(): CourseObjectiveInterface
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

    public function addDescendant(CourseObjectiveInterface $descendant)
    {
        if (!$this->descendants->contains($descendant)) {
            $this->descendants->add($descendant);
        }
    }

    public function removeDescendant(CourseObjectiveInterface $descendant)
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
