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
use App\Annotation as IS;
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
 * @IS\Entity
 */
#[ORM\Table(name: 'mesh_descriptor')]
#[ORM\Entity(repositoryClass: MeshDescriptorRepository::class)]
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
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=12)
     * })
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'mesh_descriptor_uid', type: 'string', length: 12)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    protected $id;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=192)
     * })
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(type: 'string', length: 192)]
    protected $name;
    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=65000)
     * })
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'annotation', type: 'text', nullable: true)]
    protected $annotation;
    /**
     * @var DateTime
     * @IS\Expose
     * @IS\ReadOnly
     * @IS\Type("dateTime")
     */
    #[ORM\Column(name: 'created_at', type: 'datetime')]
    protected $createdAt;
    /**
     * @var DateTime
     * @IS\Expose
     * @IS\ReadOnly
     * @IS\Type("dateTime")
     */
    #[ORM\Column(name: 'updated_at', type: 'datetime')]
    protected $updatedAt;
    /**
     * @var bool
     * @IS\Expose
     * @IS\Type("boolean")
     */
    #[ORM\Column(name: 'deleted', type: 'boolean')]
    protected $deleted;
    /**
     * @var Collection
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'Course', mappedBy: 'meshDescriptors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $courses;
    /**
     * @var Collection
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'Session', mappedBy: 'meshDescriptors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $sessions;
    /**
     * @var Collection
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'MeshConcept', mappedBy: 'descriptors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $concepts;
    /**
     * @var Collection
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'MeshQualifier', mappedBy: 'descriptors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $qualifiers;
    /**
     * @var Collection
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(targetEntity: 'MeshTree', mappedBy: 'descriptor')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $trees;
    /**
     * @var Collection
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'SessionLearningMaterial', mappedBy: 'meshDescriptors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $sessionLearningMaterials;
    /**
     * @var Collection
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'CourseLearningMaterial', mappedBy: 'meshDescriptors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $courseLearningMaterials;
    /**
     * @var MeshPreviousIndexingInterface
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\OneToOne(targetEntity: 'MeshPreviousIndexing', mappedBy: 'descriptor')]
    protected $previousIndexing;
    /**
     * @var Collection
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'SessionObjective', mappedBy: 'meshDescriptors')]
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
    protected $sessionObjectives;
    /**
     * @var Collection
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'CourseObjective', mappedBy: 'meshDescriptors')]
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
    protected $courseObjectives;
    /**
     * @var Collection
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'ProgramYearObjective', mappedBy: 'meshDescriptors')]
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
    protected $programYearObjectives;
    /**
     * Constructor
     */
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
    /**
     * @return string
     */
    public function getAnnotation()
    {
        return $this->annotation;
    }
    /**
     * @inheritdoc
     */
    public function setSessionLearningMaterials(Collection $sessionLearningMaterials)
    {
        $this->sessionLearningMaterials = new ArrayCollection();

        foreach ($sessionLearningMaterials as $sessionLearningMaterial) {
            $this->addSessionLearningMaterial($sessionLearningMaterial);
        }
    }
    /**
     * @inheritdoc
     */
    public function addSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial)
    {
        if (!$this->sessionLearningMaterials->contains($sessionLearningMaterial)) {
            $this->sessionLearningMaterials->add($sessionLearningMaterial);
            $sessionLearningMaterial->addMeshDescriptor($this);
        }
    }
    /**
     * @inheritdoc
     */
    public function removeSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial)
    {
        if ($this->sessionLearningMaterials->contains($sessionLearningMaterial)) {
            $this->sessionLearningMaterials->removeElement($sessionLearningMaterial);
            $sessionLearningMaterial->removeMeshDescriptor($this);
        }
    }
    /**
     * @inheritdoc
     */
    public function getSessionLearningMaterials()
    {
        return $this->sessionLearningMaterials;
    }
    /**
     * @inheritdoc
     */
    public function setCourseLearningMaterials(Collection $courseLearningMaterials)
    {
        $this->courseLearningMaterials = new ArrayCollection();

        foreach ($courseLearningMaterials as $courseLearningMaterial) {
            $this->addCourseLearningMaterial($courseLearningMaterial);
        }
    }
    /**
     * @inheritdoc
     */
    public function addCourseLearningMaterial(CourseLearningMaterialInterface $courseLearningMaterial)
    {
        if (!$this->courseLearningMaterials->contains($courseLearningMaterial)) {
            $this->courseLearningMaterials->add($courseLearningMaterial);
            $courseLearningMaterial->addMeshDescriptor($this);
        }
    }
    /**
     * @inheritdoc
     */
    public function removeCourseLearningMaterial(CourseLearningMaterialInterface $courseLearningMaterial)
    {
        if ($this->courseLearningMaterials->contains($courseLearningMaterial)) {
            $this->courseLearningMaterials->removeElement($courseLearningMaterial);
            $courseLearningMaterial->removeMeshDescriptor($this);
        }
    }
    /**
     * @inheritdoc
     */
    public function getCourseLearningMaterials()
    {
        return $this->courseLearningMaterials;
    }
    /**
     * @inheritdoc
     */
    public function setQualifiers(Collection $qualifiers)
    {
        $this->qualifiers = new ArrayCollection();

        foreach ($qualifiers as $qualifier) {
            $this->addQualifier($qualifier);
        }
    }
    /**
     * @inheritdoc
     */
    public function addQualifier(MeshQualifierInterface $qualifier)
    {
        if (!$this->qualifiers->contains($qualifier)) {
            $this->qualifiers->add($qualifier);
            $qualifier->addDescriptor($this);
        }
    }
    /**
     * @inheritdoc
     */
    public function removeQualifier(MeshQualifierInterface $qualifier)
    {
        if ($this->qualifiers->contains($qualifier)) {
            $this->qualifiers->removeElement($qualifier);
            $qualifier->removeDescriptor($this);
        }
    }
    /**
     * @inheritdoc
     */
    public function getQualifiers()
    {
        return $this->qualifiers;
    }
    /**
     * @inheritdoc
     */
    public function setTrees(Collection $trees)
    {
        $this->trees = new ArrayCollection();

        foreach ($trees as $tree) {
            $this->addTree($tree);
        }
    }
    /**
     * @inheritdoc
     */
    public function addTree(MeshTreeInterface $tree)
    {
        if (!$this->trees->contains($tree)) {
            $this->trees->add($tree);
        }
    }
    /**
     * @param MeshTreeInterface $tree
     */
    public function removeTree(MeshTreeInterface $tree)
    {
        if ($this->trees->contains($tree)) {
            $this->trees->removeElement($tree);
        }
    }
    /**
     * @inheritdoc
     */
    public function getTrees()
    {
        return $this->trees;
    }
    /**
     * @inheritdoc
     */
    public function setPreviousIndexing(MeshPreviousIndexingInterface $previousIndexing = null)
    {
        $this->previousIndexing = $previousIndexing;
    }
    /**
     * @inheritdoc
     */
    public function getPreviousIndexing()
    {
        return $this->previousIndexing;
    }
    /**
     * @inheritdoc
     */
    public function addCourse(CourseInterface $course)
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
            $course->addMeshDescriptor($this);
        }
    }
    /**
     * @inheritdoc
     */
    public function removeCourse(CourseInterface $course)
    {
        if ($this->courses->contains($course)) {
            $this->courses->removeElement($course);
            $course->removeMeshDescriptor($this);
        }
    }
    /**
     * @inheritdoc
     */
    public function addSession(SessionInterface $session)
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->addMeshDescriptor($this);
        }
    }
    /**
     * @inheritdoc
     */
    public function removeSession(SessionInterface $session)
    {
        if ($this->sessions->contains($session)) {
            $this->sessions->removeElement($session);
            $session->removeMeshDescriptor($this);
        }
    }
    /**
     * @inheritdoc
     */
    public function addConcept(MeshConceptInterface $concept)
    {
        if (!$this->concepts->contains($concept)) {
            $this->concepts->add($concept);
            $concept->addDescriptor($this);
        }
    }
    /**
     * @inheritdoc
     */
    public function removeConcept(MeshConceptInterface $concept)
    {
        if ($this->concepts->contains($concept)) {
            $this->concepts->removeElement($concept);
            $concept->removeDescriptor($this);
        }
    }
    /**
     * @inheritdoc
     */
    public function isDeleted()
    {
        return $this->deleted;
    }
    /**
     * @inheritdoc
     */
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
            ->map(function (CourseLearningMaterialInterface $clm) {
                return $clm->getCourse();
            });

        $sessionLMCourses = $this->sessionLearningMaterials
            ->map(function (SessionLearningMaterialInterface $slm) {
                return $slm->getSession()->getCourse();
            });

        $sessionCourses = $this->sessions
            ->map(function (SessionInterface $session) {
                return $session->getCourse();
            });

        $objectiveCourses = $this->courseObjectives
            ->map(function (CourseObjectiveInterface $objective) {
                return $objective->getIndexableCourses();
            });
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
