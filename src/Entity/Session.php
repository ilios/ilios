<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\DescribableNullableEntity;
use App\Traits\SessionObjectivesEntity;
use App\Traits\StudentAdvisorsEntity;
use App\Traits\TitledNullableEntity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\AdministratorsEntity;
use App\Traits\CategorizableEntity;
use App\Traits\MeshDescriptorsEntity;
use App\Traits\PublishableEntity;
use App\Traits\SequenceBlocksEntity;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Attributes as IA;
use App\Traits\StringableIdEntity;
use App\Traits\TimestampableEntity;
use App\Traits\OfferingsEntity;
use App\Traits\IdentifiableEntity;
use App\Repository\SessionRepository;

#[ORM\Table(name: 'session')]
#[ORM\Index(columns: ['session_type_id'], name: 'session_type_id_k')]
#[ORM\Index(columns: ['course_id'], name: 'course_id_k')]
#[ORM\Index(columns: ['session_id', 'course_id', 'session_type_id', 'title'], name: 'session_course_type_title_k')]
#[ORM\Entity(repositoryClass: SessionRepository::class)]
#[IA\Entity]
class Session implements SessionInterface
{
    use IdentifiableEntity;
    use TitledNullableEntity;
    use StringableIdEntity;
    use TimestampableEntity;
    use OfferingsEntity;
    use PublishableEntity;
    use CategorizableEntity;
    use MeshDescriptorsEntity;
    use SequenceBlocksEntity;
    use AdministratorsEntity;
    use StudentAdvisorsEntity;
    use SessionObjectivesEntity;
    use DescribableNullableEntity;

    #[ORM\Column(name: 'session_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected int $id;

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

    #[ORM\ManyToOne(targetEntity: 'SessionType', inversedBy: 'sessions')]
    #[ORM\JoinColumn(name: 'session_type_id', referencedColumnName: 'session_type_id', nullable: false)]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected SessionTypeInterface $sessionType;

    #[ORM\ManyToOne(targetEntity: 'Course', inversedBy: 'sessions')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'course_id', nullable: false, onDelete: 'CASCADE')]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected CourseInterface $course;

    #[ORM\OneToOne(mappedBy: 'session', targetEntity: 'IlmSession')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected ?IlmSessionInterface $ilmSession = null;

    #[ORM\ManyToMany(targetEntity: 'Term', inversedBy: 'sessions')]
    #[ORM\JoinTable(name: 'session_x_term')]
    #[ORM\JoinColumn(name: 'session_id', referencedColumnName: 'session_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'term_id', referencedColumnName: 'term_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $terms;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: 'SessionObjective')]
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $sessionObjectives;

    #[ORM\ManyToMany(targetEntity: 'MeshDescriptor', inversedBy: 'sessions')]
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

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: 'SessionLearningMaterial')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $learningMaterials;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: 'Offering')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $offerings;

    #[ORM\ManyToMany(targetEntity: 'CurriculumInventorySequenceBlock', mappedBy: 'sessions')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected Collection $sequenceBlocks;

    #[ORM\ManyToMany(targetEntity: 'CurriculumInventorySequenceBlock', mappedBy: 'excludedSessions')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected Collection $excludedSequenceBlocks;

    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'administeredSessions')]
    #[ORM\JoinTable(name: 'session_administrator')]
    #[ORM\JoinColumn(name: 'session_id', referencedColumnName: 'session_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $administrators;

    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'studentAdvisedSessions')]
    #[ORM\JoinTable(name: 'session_student_advisor')]
    #[ORM\JoinColumn(name: 'session_id', referencedColumnName: 'session_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $studentAdvisors;

    #[ORM\ManyToOne(targetEntity: 'Session', inversedBy: 'prerequisites')]
    #[ORM\JoinColumn(name: 'postrequisite_id', referencedColumnName: 'session_id', onDelete: 'SET NULL')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected ?SessionInterface $postrequisite = null;

    #[ORM\OneToMany(mappedBy: 'postrequisite', targetEntity: 'Session')]
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

    public function setAttireRequired(?bool $attireRequired): void
    {
        $this->attireRequired = $attireRequired;
    }

    public function isAttireRequired(): ?bool
    {
        return $this->attireRequired;
    }

    public function setEquipmentRequired(?bool $equipmentRequired): void
    {
        $this->equipmentRequired = $equipmentRequired;
    }

    public function isEquipmentRequired(): ?bool
    {
        return $this->equipmentRequired;
    }

    public function setSupplemental(?bool $supplemental): void
    {
        $this->supplemental = $supplemental;
    }

    public function isSupplemental(): ?bool
    {
        return $this->supplemental;
    }

    public function setAttendanceRequired(?bool $attendanceRequired): void
    {
        $this->attendanceRequired = $attendanceRequired;
    }

    public function isAttendanceRequired(): ?bool
    {
        return $this->attendanceRequired;
    }

    public function getInstructionalNotes(): ?string
    {
        return $this->instructionalNotes;
    }

    public function setInstructionalNotes(?string $instructionalNotes = null): void
    {
        $this->instructionalNotes = $instructionalNotes;
    }

    public function setSessionType(SessionTypeInterface $sessionType): void
    {
        $this->sessionType = $sessionType;
    }

    public function getSessionType(): SessionTypeInterface
    {
        return $this->sessionType;
    }

    public function setCourse(CourseInterface $course): void
    {
        $this->course = $course;
    }

    public function getCourse(): CourseInterface
    {
        return $this->course;
    }

    public function setIlmSession(?IlmSessionInterface $ilmSession = null): void
    {
        $this->ilmSession = $ilmSession;
        $ilmSession?->setSession($this);
    }

    public function getIlmSession(): ?IlmSessionInterface
    {
        return $this->ilmSession;
    }

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

    public function addLearningMaterial(SessionLearningMaterialInterface $learningMaterial): void
    {
        if (!$this->learningMaterials->contains($learningMaterial)) {
            $this->learningMaterials->add($learningMaterial);
        }
    }

    public function removeLearningMaterial(SessionLearningMaterialInterface $learningMaterial): void
    {
        $this->learningMaterials->removeElement($learningMaterial);
    }

    public function getLearningMaterials(): Collection
    {
        return $this->learningMaterials;
    }

    public function getSchool(): SchoolInterface
    {
        return $this->course->getSchool();
    }

    public function addAdministrator(UserInterface $administrator): void
    {
        if (!$this->administrators->contains($administrator)) {
            $this->administrators->add($administrator);
            $administrator->addAdministeredSession($this);
        }
    }

    public function removeAdministrator(UserInterface $administrator): void
    {
        if ($this->administrators->contains($administrator)) {
            $this->administrators->removeElement($administrator);
            $administrator->removeAdministeredSession($this);
        }
    }
    public function addStudentAdvisor(UserInterface $studentAdvisor): void
    {
        if (!$this->studentAdvisors->contains($studentAdvisor)) {
            $this->studentAdvisors->add($studentAdvisor);
            $studentAdvisor->addStudentAdvisedSession($this);
        }
    }
    public function removeStudentAdvisor(UserInterface $studentAdvisor): void
    {
        if ($this->studentAdvisors->contains($studentAdvisor)) {
            $this->studentAdvisors->removeElement($studentAdvisor);
            $studentAdvisor->removeStudentAdvisedSession($this);
        }
    }

    public function setExcludedSequenceBlocks(Collection $sequenceBlocks): void
    {
        $this->sequenceBlocks = new ArrayCollection();

        foreach ($sequenceBlocks as $sequenceBlock) {
            $this->addExcludedSequenceBlock($sequenceBlock);
        }
    }

    public function addExcludedSequenceBlock(CurriculumInventorySequenceBlockInterface $sequenceBlock): void
    {
        if (!$this->excludedSequenceBlocks->contains($sequenceBlock)) {
            $this->excludedSequenceBlocks->add($sequenceBlock);
        }
    }

    public function removeExcludedSequenceBlock(CurriculumInventorySequenceBlockInterface $sequenceBlock): void
    {
        $this->excludedSequenceBlocks->removeElement($sequenceBlock);
    }

    public function getExcludedSequenceBlocks(): Collection
    {
        return $this->excludedSequenceBlocks;
    }

    public function setPostrequisite(?SessionInterface $postrequisite = null): void
    {
        $this->postrequisite = $postrequisite;
    }

    public function getPostrequisite(): ?SessionInterface
    {
        return $this->postrequisite;
    }

    public function setPrerequisites(Collection $prerequisites): void
    {
        $this->prerequisites = new ArrayCollection();

        foreach ($prerequisites as $prerequisite) {
            $this->addPrerequisite($prerequisite);
        }
    }

    public function addPrerequisite(SessionInterface $prerequisite): void
    {
        if (!$this->prerequisites->contains($prerequisite)) {
            $this->prerequisites->add($prerequisite);
            $prerequisite->setPostrequisite($this);
        }
    }

    public function removePrerequisite(SessionInterface $prerequisite): void
    {
        $this->prerequisites->removeElement($prerequisite);
    }

    public function getPrerequisites(): Collection
    {
        return $this->prerequisites;
    }

    public function getIndexableCourses(): array
    {
        return [$this->course];
    }
}
