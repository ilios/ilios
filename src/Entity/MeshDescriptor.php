<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\CreatedAtEntity;
use App\Traits\CourseObjectivesEntity;
use App\Traits\ProgramYearObjectivesEntity;
use App\Traits\SessionObjectivesEntity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\ConceptsEntity;
use App\Attribute as IA;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\NameableEntity;
use App\Traits\StringableIdEntity;
use App\Traits\TimestampableEntity;
use App\Traits\CoursesEntity;
use App\Traits\SessionsEntity;
use App\Repository\MeshDescriptorRepository;

/**
 * Class MeshDescriptor
 */
#[ORM\Table(name: 'mesh_descriptor')]
#[ORM\Entity(repositoryClass: MeshDescriptorRepository::class)]
#[IA\Entity]
class MeshDescriptor implements MeshDescriptorInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;
    use NameableEntity;
    use TimestampableEntity;
    use CoursesEntity;
    use SessionsEntity;
    use ConceptsEntity;
    use CreatedAtEntity;
    use SessionObjectivesEntity;
    use CourseObjectivesEntity;
    use ProgramYearObjectivesEntity;

    /**
     * @var string
     */
    #[ORM\Column(name: 'mesh_descriptor_uid', type: 'string', length: 12)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\AtLeastOneOf([
        new Assert\Blank(),
        new Assert\Length(min: 1, max: 12),
    ])]
    protected $id;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 192)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 192)]
    protected $name;

    /**
     * @var string
     */
    #[ORM\Column(name: 'annotation', type: 'text', nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\AtLeastOneOf([
        new Assert\Blank(),
        new Assert\Length(min: 1, max: 65000),
    ])]
    protected $annotation;

    /**
     * @var DateTime
     */
    #[ORM\Column(name: 'created_at', type: 'datetime')]
    #[IA\Expose]
    #[IA\OnlyReadable]
    #[IA\Type('dateTime')]
    protected $createdAt;

    /**
     * @var DateTime
     */
    #[ORM\Column(name: 'updated_at', type: 'datetime')]
    #[IA\Expose]
    #[IA\OnlyReadable]
    #[IA\Type('dateTime')]
    protected $updatedAt;

    /**
     * @var bool
     */
    #[ORM\Column(name: 'deleted', type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    protected $deleted;

    /**
     * @var Collection
     */
    #[ORM\ManyToMany(targetEntity: 'Course', mappedBy: 'meshDescriptors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $courses;

    /**
     * @var Collection
     */
    #[ORM\ManyToMany(targetEntity: 'Session', mappedBy: 'meshDescriptors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $sessions;

    /**
     * @var Collection
     */
    #[ORM\ManyToMany(targetEntity: 'MeshConcept', mappedBy: 'descriptors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $concepts;

    /**
     * @var Collection
     */
    #[ORM\ManyToMany(targetEntity: 'MeshQualifier', mappedBy: 'descriptors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $qualifiers;

    /**
     * @var Collection
     */
    #[ORM\OneToMany(mappedBy: 'descriptor', targetEntity: 'MeshTree')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $trees;

    /**
     * @var Collection
     */
    #[ORM\ManyToMany(targetEntity: 'SessionLearningMaterial', mappedBy: 'meshDescriptors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $sessionLearningMaterials;

    /**
     * @var Collection
     */
    #[ORM\ManyToMany(targetEntity: 'CourseLearningMaterial', mappedBy: 'meshDescriptors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $courseLearningMaterials;

    /**
     * @var MeshPreviousIndexingInterface
     */
    #[ORM\OneToOne(targetEntity: 'MeshPreviousIndexing', mappedBy: 'descriptor')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected $previousIndexing;

    /**
     * @var Collection
     */
    #[ORM\ManyToMany(targetEntity: 'SessionObjective', mappedBy: 'meshDescriptors')]
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $sessionObjectives;

    /**
     * @var Collection
     */
    #[ORM\ManyToMany(targetEntity: 'CourseObjective', mappedBy: 'meshDescriptors')]
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $courseObjectives;

    /**
     * @var Collection
     */
    #[ORM\ManyToMany(targetEntity: 'ProgramYearObjective', mappedBy: 'meshDescriptors')]
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $programYearObjectives;

    public function __construct()
    {
        $this->courses = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->sessionLearningMaterials = new ArrayCollection();
        $this->courseLearningMaterials = new ArrayCollection();
        $this->programYearObjectives = new ArrayCollection();
        $this->courseObjectives = new ArrayCollection();
        $this->sessionObjectives = new ArrayCollection();
        $this->trees = new ArrayCollection();
        $this->concepts = new ArrayCollection();
        $this->qualifiers = new ArrayCollection();
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
        $this->deleted = false;
    }

    /**
     * @param string $annotation
     */
    public function setAnnotation($annotation)
    {
        $this->annotation = $annotation;
    }

    public function getAnnotation(): string
    {
        return $this->annotation;
    }

    public function setSessionLearningMaterials(Collection $sessionLearningMaterials)
    {
        $this->sessionLearningMaterials = new ArrayCollection();

        foreach ($sessionLearningMaterials as $sessionLearningMaterial) {
            $this->addSessionLearningMaterial($sessionLearningMaterial);
        }
    }

    public function addSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial)
    {
        if (!$this->sessionLearningMaterials->contains($sessionLearningMaterial)) {
            $this->sessionLearningMaterials->add($sessionLearningMaterial);
            $sessionLearningMaterial->addMeshDescriptor($this);
        }
    }

    public function removeSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial)
    {
        if ($this->sessionLearningMaterials->contains($sessionLearningMaterial)) {
            $this->sessionLearningMaterials->removeElement($sessionLearningMaterial);
            $sessionLearningMaterial->removeMeshDescriptor($this);
        }
    }

    public function getSessionLearningMaterials(): Collection
    {
        return $this->sessionLearningMaterials;
    }

    public function setCourseLearningMaterials(Collection $courseLearningMaterials)
    {
        $this->courseLearningMaterials = new ArrayCollection();

        foreach ($courseLearningMaterials as $courseLearningMaterial) {
            $this->addCourseLearningMaterial($courseLearningMaterial);
        }
    }

    public function addCourseLearningMaterial(CourseLearningMaterialInterface $courseLearningMaterial)
    {
        if (!$this->courseLearningMaterials->contains($courseLearningMaterial)) {
            $this->courseLearningMaterials->add($courseLearningMaterial);
            $courseLearningMaterial->addMeshDescriptor($this);
        }
    }

    public function removeCourseLearningMaterial(CourseLearningMaterialInterface $courseLearningMaterial)
    {
        if ($this->courseLearningMaterials->contains($courseLearningMaterial)) {
            $this->courseLearningMaterials->removeElement($courseLearningMaterial);
            $courseLearningMaterial->removeMeshDescriptor($this);
        }
    }

    public function getCourseLearningMaterials(): Collection
    {
        return $this->courseLearningMaterials;
    }

    public function setQualifiers(Collection $qualifiers)
    {
        $this->qualifiers = new ArrayCollection();

        foreach ($qualifiers as $qualifier) {
            $this->addQualifier($qualifier);
        }
    }

    public function addQualifier(MeshQualifierInterface $qualifier)
    {
        if (!$this->qualifiers->contains($qualifier)) {
            $this->qualifiers->add($qualifier);
            $qualifier->addDescriptor($this);
        }
    }

    public function removeQualifier(MeshQualifierInterface $qualifier)
    {
        if ($this->qualifiers->contains($qualifier)) {
            $this->qualifiers->removeElement($qualifier);
            $qualifier->removeDescriptor($this);
        }
    }

    public function getQualifiers(): Collection
    {
        return $this->qualifiers;
    }

    public function setTrees(Collection $trees)
    {
        $this->trees = new ArrayCollection();

        foreach ($trees as $tree) {
            $this->addTree($tree);
        }
    }

    public function addTree(MeshTreeInterface $tree)
    {
        if (!$this->trees->contains($tree)) {
            $this->trees->add($tree);
        }
    }

    public function removeTree(MeshTreeInterface $tree)
    {
        if ($this->trees->contains($tree)) {
            $this->trees->removeElement($tree);
        }
    }

    public function getTrees(): Collection
    {
        return $this->trees;
    }

    public function setPreviousIndexing(MeshPreviousIndexingInterface $previousIndexing = null)
    {
        $this->previousIndexing = $previousIndexing;
    }

    public function getPreviousIndexing(): MeshPreviousIndexingInterface
    {
        return $this->previousIndexing;
    }

    public function addCourse(CourseInterface $course)
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
            $course->addMeshDescriptor($this);
        }
    }

    public function removeCourse(CourseInterface $course)
    {
        if ($this->courses->contains($course)) {
            $this->courses->removeElement($course);
            $course->removeMeshDescriptor($this);
        }
    }

    public function addSession(SessionInterface $session)
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->addMeshDescriptor($this);
        }
    }

    public function removeSession(SessionInterface $session)
    {
        if ($this->sessions->contains($session)) {
            $this->sessions->removeElement($session);
            $session->removeMeshDescriptor($this);
        }
    }

    public function addConcept(MeshConceptInterface $concept)
    {
        if (!$this->concepts->contains($concept)) {
            $this->concepts->add($concept);
            $concept->addDescriptor($this);
        }
    }

    public function removeConcept(MeshConceptInterface $concept)
    {
        if ($this->concepts->contains($concept)) {
            $this->concepts->removeElement($concept);
            $concept->removeDescriptor($this);
        }
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @inheritDoc
     */
    public function getIndexableCourses(): array
    {
        $courseLmCourses = $this->courseLearningMaterials
            ->map(fn(CourseLearningMaterialInterface $clm) => $clm->getCourse());

        $sessionLMCourses = $this->sessionLearningMaterials
            ->map(fn(SessionLearningMaterialInterface $slm) => $slm->getSession()->getCourse());

        $sessionCourses = $this->sessions
            ->map(fn(SessionInterface $session) => $session->getCourse());

        $objectiveCourses = $this->courseObjectives
            ->map(fn(CourseObjectiveInterface $objective) => $objective->getIndexableCourses());
        $flatObjectiveCourses = count($objectiveCourses) ? array_merge(...$objectiveCourses) : [];

        return array_merge(
            $this->courses->toArray(),
            $courseLmCourses->toArray(),
            $sessionLMCourses->toArray(),
            $sessionCourses->toArray(),
            $flatObjectiveCourses
        );
    }
}
