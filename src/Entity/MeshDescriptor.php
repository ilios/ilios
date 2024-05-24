<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\CreatedAtEntity;
use App\Traits\CourseObjectivesEntity;
use App\Traits\IdentifiableStringEntity;
use App\Traits\ProgramYearObjectivesEntity;
use App\Traits\SessionObjectivesEntity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\ConceptsEntity;
use App\Attributes as IA;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\NameableEntity;
use App\Traits\TimestampableEntity;
use App\Traits\CoursesEntity;
use App\Traits\SessionsEntity;
use App\Repository\MeshDescriptorRepository;

#[ORM\Table(name: 'mesh_descriptor')]
#[ORM\Entity(repositoryClass: MeshDescriptorRepository::class)]
#[IA\Entity]
class MeshDescriptor implements MeshDescriptorInterface
{
    use IdentifiableStringEntity;
    use NameableEntity;
    use TimestampableEntity;
    use CoursesEntity;
    use SessionsEntity;
    use ConceptsEntity;
    use CreatedAtEntity;
    use SessionObjectivesEntity;
    use CourseObjectivesEntity;
    use ProgramYearObjectivesEntity;

    #[ORM\Column(name: 'mesh_descriptor_uid', type: 'string', length: 12)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 12)]
    protected string $id;

    #[ORM\Column(type: 'string', length: 192)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 192)]
    protected string $name;

    #[ORM\Column(name: 'annotation', type: 'text', nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 65000)]
    protected ?string $annotation = null;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    #[IA\Expose]
    #[IA\OnlyReadable]
    #[IA\Type('dateTime')]
    protected DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime')]
    #[IA\Expose]
    #[IA\OnlyReadable]
    #[IA\Type('dateTime')]
    protected DateTime $updatedAt;

    #[ORM\Column(name: 'deleted', type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    protected bool $deleted;

    #[ORM\ManyToMany(targetEntity: 'Course', mappedBy: 'meshDescriptors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $courses;

    #[ORM\ManyToMany(targetEntity: 'Session', mappedBy: 'meshDescriptors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $sessions;

    #[ORM\ManyToMany(targetEntity: 'MeshConcept', mappedBy: 'descriptors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $concepts;

    #[ORM\ManyToMany(targetEntity: 'MeshQualifier', mappedBy: 'descriptors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $qualifiers;

    #[ORM\OneToMany(mappedBy: 'descriptor', targetEntity: 'MeshTree')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $trees;

    #[ORM\ManyToMany(targetEntity: 'SessionLearningMaterial', mappedBy: 'meshDescriptors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $sessionLearningMaterials;

    #[ORM\ManyToMany(targetEntity: 'CourseLearningMaterial', mappedBy: 'meshDescriptors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $courseLearningMaterials;

    #[ORM\OneToOne(mappedBy: 'descriptor', targetEntity: 'MeshPreviousIndexing')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected MeshPreviousIndexingInterface $previousIndexing;

    #[ORM\ManyToMany(targetEntity: 'SessionObjective', mappedBy: 'meshDescriptors')]
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $sessionObjectives;

    #[ORM\ManyToMany(targetEntity: 'CourseObjective', mappedBy: 'meshDescriptors')]
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $courseObjectives;

    #[ORM\ManyToMany(targetEntity: 'ProgramYearObjective', mappedBy: 'meshDescriptors')]
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $programYearObjectives;

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

    public function setAnnotation(?string $annotation): void
    {
        $this->annotation = $annotation;
    }

    public function getAnnotation(): ?string
    {
        return $this->annotation;
    }

    public function setSessionLearningMaterials(Collection $sessionLearningMaterials): void
    {
        $this->sessionLearningMaterials = new ArrayCollection();

        foreach ($sessionLearningMaterials as $sessionLearningMaterial) {
            $this->addSessionLearningMaterial($sessionLearningMaterial);
        }
    }

    public function addSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial): void
    {
        if (!$this->sessionLearningMaterials->contains($sessionLearningMaterial)) {
            $this->sessionLearningMaterials->add($sessionLearningMaterial);
            $sessionLearningMaterial->addMeshDescriptor($this);
        }
    }

    public function removeSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial): void
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

    public function setCourseLearningMaterials(Collection $courseLearningMaterials): void
    {
        $this->courseLearningMaterials = new ArrayCollection();

        foreach ($courseLearningMaterials as $courseLearningMaterial) {
            $this->addCourseLearningMaterial($courseLearningMaterial);
        }
    }

    public function addCourseLearningMaterial(CourseLearningMaterialInterface $courseLearningMaterial): void
    {
        if (!$this->courseLearningMaterials->contains($courseLearningMaterial)) {
            $this->courseLearningMaterials->add($courseLearningMaterial);
            $courseLearningMaterial->addMeshDescriptor($this);
        }
    }

    public function removeCourseLearningMaterial(CourseLearningMaterialInterface $courseLearningMaterial): void
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

    public function setQualifiers(Collection $qualifiers): void
    {
        $this->qualifiers = new ArrayCollection();

        foreach ($qualifiers as $qualifier) {
            $this->addQualifier($qualifier);
        }
    }

    public function addQualifier(MeshQualifierInterface $qualifier): void
    {
        if (!$this->qualifiers->contains($qualifier)) {
            $this->qualifiers->add($qualifier);
            $qualifier->addDescriptor($this);
        }
    }

    public function removeQualifier(MeshQualifierInterface $qualifier): void
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

    public function setTrees(Collection $trees): void
    {
        $this->trees = new ArrayCollection();

        foreach ($trees as $tree) {
            $this->addTree($tree);
        }
    }

    public function addTree(MeshTreeInterface $tree): void
    {
        if (!$this->trees->contains($tree)) {
            $this->trees->add($tree);
        }
    }

    public function removeTree(MeshTreeInterface $tree): void
    {
        if ($this->trees->contains($tree)) {
            $this->trees->removeElement($tree);
        }
    }

    public function getTrees(): Collection
    {
        return $this->trees;
    }

    public function setPreviousIndexing(?MeshPreviousIndexingInterface $previousIndexing = null): void
    {
        $this->previousIndexing = $previousIndexing;
    }

    public function getPreviousIndexing(): MeshPreviousIndexingInterface
    {
        return $this->previousIndexing;
    }

    public function addCourse(CourseInterface $course): void
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
            $course->addMeshDescriptor($this);
        }
    }

    public function removeCourse(CourseInterface $course): void
    {
        if ($this->courses->contains($course)) {
            $this->courses->removeElement($course);
            $course->removeMeshDescriptor($this);
        }
    }

    public function addSession(SessionInterface $session): void
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->addMeshDescriptor($this);
        }
    }

    public function removeSession(SessionInterface $session): void
    {
        if ($this->sessions->contains($session)) {
            $this->sessions->removeElement($session);
            $session->removeMeshDescriptor($this);
        }
    }

    public function addConcept(MeshConceptInterface $concept): void
    {
        if (!$this->concepts->contains($concept)) {
            $this->concepts->add($concept);
            $concept->addDescriptor($this);
        }
    }

    public function removeConcept(MeshConceptInterface $concept): void
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

    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }

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
        $flatObjectiveCourses = count($objectiveCourses) ? array_merge(...$objectiveCourses->toArray()) : [];

        return array_merge(
            $this->courses->toArray(),
            $courseLmCourses->toArray(),
            $sessionLMCourses->toArray(),
            $sessionCourses->toArray(),
            $flatObjectiveCourses
        );
    }

    public function __toString(): string
    {
        return $this->id ?? '';
    }
}
