<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\TitledNullableEntity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Attributes as IA;
use App\Repository\SessionRepository;
use Override;

#[ORM\Table(name: 'session')]
#[ORM\Index(columns: ['session_type_id'], name: 'session_type_id_k')]
#[ORM\Index(columns: ['course_id'], name: 'course_id_k')]
#[ORM\Index(columns: ['session_id', 'course_id', 'session_type_id', 'title'], name: 'session_course_type_title_k')]
#[ORM\Entity(repositoryClass: SessionRepository::class)]
#[IA\Entity]
class Session implements SessionInterface
{
    use TitledNullableEntity;

    #[ORM\Column(name: 'session_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected ?int $id;

    #[ORM\Column(type: 'string', length: 200, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 200)]
    protected ?string $title = null;

    #[ORM\Column(name: 'attire_required', type: 'boolean', nullable: true)]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\Type(type: 'bool')]
    protected ?bool $attireRequired = null;

    #[ORM\Column(name: 'equipment_required', type: 'boolean', nullable: true)]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\Type(type: 'bool')]
    protected ?bool $equipmentRequired = null;

    #[ORM\Column(name: 'supplemental', type: 'boolean', nullable: true)]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\Type(type: 'bool')]
    protected ?bool $supplemental = null;

    #[ORM\Column(name: 'attendance_required', type: 'boolean', nullable: true)]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\Type(type: 'bool')]
    protected ?bool $attendanceRequired = null;

    #[ORM\Column(name: 'published_as_tbd', type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\NotNull]
    #[Assert\Type(type: 'bool')]
    protected bool $publishedAsTbd;

    #[ORM\Column(type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\NotNull]
    #[Assert\Type(type: 'bool')]
    protected bool $published;

    #[ORM\Column(name: 'last_updated_on', type: 'datetime')]
    #[IA\Expose]
    #[IA\OnlyReadable]
    #[IA\Type('dateTime')]
    #[Assert\NotBlank]
    protected DateTime $updatedAt;

    #[ORM\Column(name: 'instructionalNotes', type: 'text', nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[IA\RemoveMarkup]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 65000)]
    protected ?string $instructionalNotes = null;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[IA\RemoveMarkup]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 65000)]
    protected ?string $description = null;

    #[ORM\ManyToOne(targetEntity: SessionType::class, inversedBy: 'sessions')]
    #[ORM\JoinColumn(name: 'session_type_id', referencedColumnName: 'session_type_id', nullable: false)]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected SessionTypeInterface $sessionType;

    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'sessions')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'course_id', nullable: false, onDelete: 'CASCADE')]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected CourseInterface $course;

    #[ORM\OneToOne(mappedBy: 'session', targetEntity: IlmSession::class)]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected ?IlmSessionInterface $ilmSession = null;

    #[ORM\ManyToMany(targetEntity: Term::class, inversedBy: 'sessions')]
    #[ORM\JoinTable(name: 'session_x_term')]
    #[ORM\JoinColumn(name: 'session_id', referencedColumnName: 'session_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'term_id', referencedColumnName: 'term_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $terms;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: SessionObjective::class)]
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $sessionObjectives;

    #[ORM\ManyToMany(targetEntity: MeshDescriptor::class, inversedBy: 'sessions')]
    #[ORM\JoinTable(name: 'session_x_mesh')]
    #[ORM\JoinColumn(name: 'session_id', referencedColumnName: 'session_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(
        name: 'mesh_descriptor_uid',
        referencedColumnName: 'mesh_descriptor_uid',
        onDelete: 'CASCADE'
    )]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $meshDescriptors;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: SessionLearningMaterial::class)]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $learningMaterials;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: Offering::class)]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $offerings;

    #[ORM\ManyToMany(targetEntity: CurriculumInventorySequenceBlock::class, mappedBy: 'sessions')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected Collection $sequenceBlocks;

    #[ORM\ManyToMany(targetEntity: CurriculumInventorySequenceBlock::class, mappedBy: 'excludedSessions')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected Collection $excludedSequenceBlocks;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'administeredSessions')]
    #[ORM\JoinTable(name: 'session_administrator')]
    #[ORM\JoinColumn(name: 'session_id', referencedColumnName: 'session_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $administrators;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'studentAdvisedSessions')]
    #[ORM\JoinTable(name: 'session_student_advisor')]
    #[ORM\JoinColumn(name: 'session_id', referencedColumnName: 'session_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $studentAdvisors;

    #[ORM\ManyToOne(targetEntity: Session::class, inversedBy: 'prerequisites')]
    #[ORM\JoinColumn(name: 'postrequisite_id', referencedColumnName: 'session_id', onDelete: 'SET NULL')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected ?SessionInterface $postrequisite = null;

    #[ORM\OneToMany(mappedBy: 'postrequisite', targetEntity: Session::class)]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $prerequisites;

    public function __construct()
    {
        $this->attireRequired = null;
        $this->equipmentRequired = null;
        $this->supplemental = null;
        $this->attendanceRequired = null;
        $this->publishedAsTbd = false;
        $this->published = false;
        $this->terms = new ArrayCollection();
        $this->sessionObjectives = new ArrayCollection();
        $this->meshDescriptors = new ArrayCollection();
        $this->offerings = new ArrayCollection();
        $this->learningMaterials = new ArrayCollection();
        $this->sequenceBlocks = new ArrayCollection();
        $this->excludedSequenceBlocks = new ArrayCollection();
        $this->administrators = new ArrayCollection();
        $this->studentAdvisors = new ArrayCollection();
        $this->prerequisites = new ArrayCollection();
        $this->updatedAt = new DateTime();
    }

    #[Override]
    public function setAttireRequired(?bool $attireRequired): void
    {
        $this->attireRequired = $attireRequired;
    }

    #[Override]
    public function isAttireRequired(): ?bool
    {
        return $this->attireRequired;
    }

    #[Override]
    public function setEquipmentRequired(?bool $equipmentRequired): void
    {
        $this->equipmentRequired = $equipmentRequired;
    }

    #[Override]
    public function isEquipmentRequired(): ?bool
    {
        return $this->equipmentRequired;
    }

    #[Override]
    public function setSupplemental(?bool $supplemental): void
    {
        $this->supplemental = $supplemental;
    }

    #[Override]
    public function isSupplemental(): ?bool
    {
        return $this->supplemental;
    }

    #[Override]
    public function setAttendanceRequired(?bool $attendanceRequired): void
    {
        $this->attendanceRequired = $attendanceRequired;
    }

    #[Override]
    public function isAttendanceRequired(): ?bool
    {
        return $this->attendanceRequired;
    }

    #[Override]
    public function getInstructionalNotes(): ?string
    {
        return $this->instructionalNotes;
    }

    #[Override]
    public function setInstructionalNotes(?string $instructionalNotes = null): void
    {
        $this->instructionalNotes = $instructionalNotes;
    }

    #[Override]
    public function setSessionType(SessionTypeInterface $sessionType): void
    {
        $this->sessionType = $sessionType;
    }

    #[Override]
    public function getSessionType(): SessionTypeInterface
    {
        return $this->sessionType;
    }

    #[Override]
    public function setCourse(CourseInterface $course): void
    {
        $this->course = $course;
    }

    #[Override]
    public function getCourse(): CourseInterface
    {
        return $this->course;
    }

    #[Override]
    public function setIlmSession(?IlmSessionInterface $ilmSession = null): void
    {
        $this->ilmSession = $ilmSession;
        $ilmSession?->setSession($this);
    }

    #[Override]
    public function getIlmSession(): ?IlmSessionInterface
    {
        return $this->ilmSession;
    }

    #[Override]
    public function setLearningMaterials(?Collection $learningMaterials = null): void
    {
        $this->learningMaterials = new ArrayCollection();
        if (is_null($learningMaterials)) {
            return;
        }

        foreach ($learningMaterials as $learningMaterial) {
            $this->addLearningMaterial($learningMaterial);
        }
    }

    #[Override]
    public function addLearningMaterial(SessionLearningMaterialInterface $learningMaterial): void
    {
        if (!$this->learningMaterials->contains($learningMaterial)) {
            $this->learningMaterials->add($learningMaterial);
        }
    }

    #[Override]
    public function removeLearningMaterial(SessionLearningMaterialInterface $learningMaterial): void
    {
        $this->learningMaterials->removeElement($learningMaterial);
    }

    #[Override]
    public function getLearningMaterials(): Collection
    {
        return $this->learningMaterials;
    }

    #[Override]
    public function getSchool(): SchoolInterface
    {
        return $this->course->getSchool();
    }

    #[Override]
    public function getAdministrators(): Collection
    {
        return $this->administrators;
    }

    #[Override]
    public function addAdministrator(UserInterface $administrator): void
    {
        if (!$this->administrators->contains($administrator)) {
            $this->administrators->add($administrator);
            $administrator->addAdministeredSession($this);
        }
    }

    #[Override]
    public function removeAdministrator(UserInterface $administrator): void
    {
        if ($this->administrators->contains($administrator)) {
            $this->administrators->removeElement($administrator);
            $administrator->removeAdministeredSession($this);
        }
    }

    #[Override]
    public function setAdministrators(Collection $administrators): void
    {
        $this->administrators = new ArrayCollection();

        foreach ($administrators as $administrator) {
            $this->addAdministrator($administrator);
        }
    }

    #[Override]
    public function addStudentAdvisor(UserInterface $studentAdvisor): void
    {
        if (!$this->studentAdvisors->contains($studentAdvisor)) {
            $this->studentAdvisors->add($studentAdvisor);
            $studentAdvisor->addStudentAdvisedSession($this);
        }
    }
    #[Override]
    public function removeStudentAdvisor(UserInterface $studentAdvisor): void
    {
        if ($this->studentAdvisors->contains($studentAdvisor)) {
            $this->studentAdvisors->removeElement($studentAdvisor);
            $studentAdvisor->removeStudentAdvisedSession($this);
        }
    }

    #[Override]
    public function setExcludedSequenceBlocks(Collection $sequenceBlocks): void
    {
        $this->sequenceBlocks = new ArrayCollection();

        foreach ($sequenceBlocks as $sequenceBlock) {
            $this->addExcludedSequenceBlock($sequenceBlock);
        }
    }

    #[Override]
    public function addExcludedSequenceBlock(CurriculumInventorySequenceBlockInterface $sequenceBlock): void
    {
        if (!$this->excludedSequenceBlocks->contains($sequenceBlock)) {
            $this->excludedSequenceBlocks->add($sequenceBlock);
        }
    }

    #[Override]
    public function removeExcludedSequenceBlock(CurriculumInventorySequenceBlockInterface $sequenceBlock): void
    {
        $this->excludedSequenceBlocks->removeElement($sequenceBlock);
    }

    #[Override]
    public function getExcludedSequenceBlocks(): Collection
    {
        return $this->excludedSequenceBlocks;
    }

    #[Override]
    public function setPostrequisite(?SessionInterface $postrequisite = null): void
    {
        $this->postrequisite = $postrequisite;
    }

    #[Override]
    public function getPostrequisite(): ?SessionInterface
    {
        return $this->postrequisite;
    }

    #[Override]
    public function setPrerequisites(Collection $prerequisites): void
    {
        $this->prerequisites = new ArrayCollection();

        foreach ($prerequisites as $prerequisite) {
            $this->addPrerequisite($prerequisite);
        }
    }

    #[Override]
    public function addPrerequisite(SessionInterface $prerequisite): void
    {
        if (!$this->prerequisites->contains($prerequisite)) {
            $this->prerequisites->add($prerequisite);
            $prerequisite->setPostrequisite($this);
        }
    }

    #[Override]
    public function removePrerequisite(SessionInterface $prerequisite): void
    {
        $this->prerequisites->removeElement($prerequisite);
    }

    #[Override]
    public function getPrerequisites(): Collection
    {
        return $this->prerequisites;
    }

    #[Override]
    public function getIndexableCourses(): array
    {
        return [$this->course];
    }
    #[Override]
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    #[Override]
    public function getId(): ?int
    {
        return $this->id;
    }

    #[Override]
    public function setTerms(?Collection $terms = null): void
    {
        $this->terms = new ArrayCollection();
        if (is_null($terms)) {
            return;
        }

        foreach ($terms as $term) {
            $this->addTerm($term);
        }
    }

    #[Override]
    public function addTerm(TermInterface $term): void
    {
        if (!$this->terms->contains($term)) {
            $this->terms->add($term);
        }
    }

    #[Override]
    public function removeTerm(TermInterface $term): void
    {
        $this->terms->removeElement($term);
    }

    #[Override]
    public function getTerms(): Collection
    {
        return $this->terms;
    }

    #[Override]
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    #[Override]
    public function getDescription(): ?string
    {
        return $this->description;
    }

    #[Override]
    public function setMeshDescriptors(Collection $meshDescriptors): void
    {
        $this->meshDescriptors = new ArrayCollection();

        foreach ($meshDescriptors as $meshDescriptor) {
            $this->addMeshDescriptor($meshDescriptor);
        }
    }

    #[Override]
    public function addMeshDescriptor(MeshDescriptorInterface $meshDescriptor): void
    {
        if (!$this->meshDescriptors->contains($meshDescriptor)) {
            $this->meshDescriptors->add($meshDescriptor);
        }
    }

    #[Override]
    public function removeMeshDescriptor(MeshDescriptorInterface $meshDescriptor): void
    {
        $this->meshDescriptors->removeElement($meshDescriptor);
    }

    #[Override]
    public function getMeshDescriptors(): Collection
    {
        return $this->meshDescriptors;
    }

    #[Override]
    public function setOfferings(Collection $offerings): void
    {
        $this->offerings = new ArrayCollection();

        foreach ($offerings as $offering) {
            $this->addOffering($offering);
        }
    }

    #[Override]
    public function addOffering(OfferingInterface $offering): void
    {
        if (!$this->offerings->contains($offering)) {
            $this->offerings->add($offering);
        }
    }

    #[Override]
    public function removeOffering(OfferingInterface $offering): void
    {
        $this->offerings->removeElement($offering);
    }

    #[Override]
    public function getOfferings(): Collection
    {
        return $this->offerings;
    }

    #[Override]
    public function setPublished(bool $published): void
    {
        $this->published = $published;
    }

    #[Override]
    public function isPublished(): bool
    {
        return $this->published;
    }

    #[Override]
    public function setPublishedAsTbd(bool $publishedAsTbd): void
    {
        $this->publishedAsTbd = $publishedAsTbd;
    }

    #[Override]
    public function isPublishedAsTbd(): bool
    {
        return $this->publishedAsTbd;
    }

    #[Override]
    public function setSequenceBlocks(Collection $sequenceBlocks): void
    {
        $this->sequenceBlocks = new ArrayCollection();

        foreach ($sequenceBlocks as $sequenceBlock) {
            $this->addSequenceBlock($sequenceBlock);
        }
    }

    #[Override]
    public function addSequenceBlock(
        CurriculumInventorySequenceBlockInterface $sequenceBlock
    ): void {
        if (!$this->sequenceBlocks->contains($sequenceBlock)) {
            $this->sequenceBlocks->add($sequenceBlock);
        }
    }

    #[Override]
    public function removeSequenceBlock(
        CurriculumInventorySequenceBlockInterface $sequenceBlock
    ): void {
        $this->sequenceBlocks->removeElement($sequenceBlock);
    }

    #[Override]
    public function getSequenceBlocks(): Collection
    {
        return $this->sequenceBlocks;
    }

    #[Override]
    public function setSessionObjectives(?Collection $sessionObjectives = null): void
    {
        $this->sessionObjectives = new ArrayCollection();
        if (is_null($sessionObjectives)) {
            return;
        }

        foreach ($sessionObjectives as $sessionObjective) {
            $this->addSessionObjective($sessionObjective);
        }
    }

    #[Override]
    public function addSessionObjective(SessionObjectiveInterface $sessionObjective): void
    {
        if (!$this->sessionObjectives->contains($sessionObjective)) {
            $this->sessionObjectives->add($sessionObjective);
        }
    }

    #[Override]
    public function removeSessionObjective(SessionObjectiveInterface $sessionObjective): void
    {
        $this->sessionObjectives->removeElement($sessionObjective);
    }

    #[Override]
    public function getSessionObjectives(): Collection
    {
        return $this->sessionObjectives;
    }

    #[Override]
    public function setStudentAdvisors(Collection $studentAdvisors): void
    {
        $this->studentAdvisors = new ArrayCollection();

        foreach ($studentAdvisors as $studentAdvisor) {
            $this->addStudentAdvisor($studentAdvisor);
        }
    }

    #[Override]
    public function getStudentAdvisors(): Collection
    {
        return $this->studentAdvisors;
    }

    #[Override]
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    #[Override]
    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    #[Override]
    public function getClassName(): string
    {
        return __CLASS__;
    }

    #[Override]
    public function __toString(): string
    {
        return isset($this->id) ? (string) $this->id : '';
    }
}
