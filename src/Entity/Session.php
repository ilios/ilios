<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\DescribableEntity;
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
use App\Attribute as IA;
use App\Traits\StringableIdEntity;
use App\Traits\TimestampableEntity;
use App\Traits\OfferingsEntity;
use App\Traits\IdentifiableEntity;
use App\Repository\SessionRepository;

/**
 * Class Session
 */
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
    use DescribableEntity;

    /**
     * @var int
     */
    #[ORM\Column(name: 'session_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected $id;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 200, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 200)]
    protected $title;

    /**
     * @var ?bool
     */
    #[ORM\Column(name: 'attire_required', type: 'boolean', nullable: true)]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\Type(type: 'bool')]
    protected $attireRequired;

    /**
     * @var ?bool
     */
    #[ORM\Column(name: 'equipment_required', type: 'boolean', nullable: true)]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\Type(type: 'bool')]
    protected $equipmentRequired;

    /**
     * @var ?bool
     */
    #[ORM\Column(name: 'supplemental', type: 'boolean', nullable: true)]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\Type(type: 'bool')]
    protected $supplemental;

    /**
     * @var ?bool
     */
    #[ORM\Column(name: 'attendance_required', type: 'boolean', nullable: true)]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\Type(type: 'bool')]
    protected $attendanceRequired;

    /**
     * @var bool
     */
    #[ORM\Column(name: 'published_as_tbd', type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\NotNull]
    #[Assert\Type(type: 'bool')]
    protected $publishedAsTbd;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\NotNull]
    #[Assert\Type(type: 'bool')]
    protected $published;

    /**
     * @var DateTime
     */
    #[ORM\Column(name: 'last_updated_on', type: 'datetime')]
    #[IA\Expose]
    #[IA\OnlyReadable]
    #[IA\Type('dateTime')]
    #[Assert\NotBlank]
    protected $updatedAt;

    /**
     * @var string
     */
    #[ORM\Column(name: 'instructionalNotes', type: 'text', nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[IA\RemoveMarkup]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 65000)]
    protected $instructionalNotes;

    /**
     * @var string
     */
    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[IA\RemoveMarkup]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 65000)]
    protected $description;

    /**
     * @var SessionTypeInterface
     */
    #[ORM\ManyToOne(targetEntity: 'SessionType', inversedBy: 'sessions')]
    #[ORM\JoinColumn(name: 'session_type_id', referencedColumnName: 'session_type_id', nullable: false)]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected $sessionType;

    /**
     * @var CourseInterface
     */
    #[ORM\ManyToOne(targetEntity: 'Course', inversedBy: 'sessions')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'course_id', nullable: false, onDelete: 'CASCADE')]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected $course;

    /**
     * @var IlmSessionInterface
     */
    #[ORM\OneToOne(mappedBy: 'session', targetEntity: 'IlmSession')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected $ilmSession;

    /**
     * @var ArrayCollection|TermInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'Term', inversedBy: 'sessions')]
    #[ORM\JoinTable(name: 'session_x_term')]
    #[ORM\JoinColumn(name: 'session_id', referencedColumnName: 'session_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'term_id', referencedColumnName: 'term_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $terms;

    /**
     * @var ArrayCollection|SessionObjectiveInterface[]
     */
    #[ORM\OneToMany(mappedBy: 'session', targetEntity: 'SessionObjective')]
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $sessionObjectives;

    /**
     * @var ArrayCollection|MeshDescriptorInterface[]
     */
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
    #[IA\Type('entityCollection')]
    protected $meshDescriptors;

    /**
     * @var ArrayCollection|SessionLearningMaterialInterface[]
     */
    #[ORM\OneToMany(mappedBy: 'session', targetEntity: 'SessionLearningMaterial')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $learningMaterials;

    /**
     * @var ArrayCollection|OfferingInterface[]
     */
    #[ORM\OneToMany(mappedBy: 'session', targetEntity: 'Offering')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $offerings;

    /**
     * @var ArrayCollection|SessionInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'CurriculumInventorySequenceBlock', mappedBy: 'sessions')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $sequenceBlocks;

    /**
     * @var ArrayCollection|SessionInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'CurriculumInventorySequenceBlock', mappedBy: 'excludedSessions')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $excludedSequenceBlocks;

    /**
     * @var ArrayCollection|UserInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'administeredSessions')]
    #[ORM\JoinTable(name: 'session_administrator')]
    #[ORM\JoinColumn(name: 'session_id', referencedColumnName: 'session_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $administrators;

    /**
     * @var ArrayCollection|UserInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'studentAdvisedSessions')]
    #[ORM\JoinTable(name: 'session_student_advisor')]
    #[ORM\JoinColumn(name: 'session_id', referencedColumnName: 'session_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $studentAdvisors;

    /**
     * @var SessionInterface
     */
    #[ORM\ManyToOne(targetEntity: 'Session', inversedBy: 'prerequisites')]
    #[ORM\JoinColumn(name: 'postrequisite_id', referencedColumnName: 'session_id', onDelete: 'SET NULL')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected $postrequisite;

    /**
     * @var ArrayCollection|SessionInterface[]
     */
    #[ORM\OneToMany(mappedBy: 'postrequisite', targetEntity: 'Session')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $prerequisites;

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

    /**
     * @param bool $attireRequired
     */
    public function setAttireRequired($attireRequired)
    {
        $this->attireRequired = $attireRequired;
    }

    public function isAttireRequired(): ?bool
    {
        return $this->attireRequired;
    }

    /**
     * @param bool $equipmentRequired
     */
    public function setEquipmentRequired($equipmentRequired)
    {
        $this->equipmentRequired = $equipmentRequired;
    }

    public function isEquipmentRequired(): ?bool
    {
        return $this->equipmentRequired;
    }

    /**
     * @param bool $supplemental
     */
    public function setSupplemental($supplemental)
    {
        $this->supplemental = $supplemental;
    }

    public function isSupplemental(): ?bool
    {
        return $this->supplemental;
    }

    /**
     * @param bool $attendanceRequired
     */
    public function setAttendanceRequired($attendanceRequired)
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

    public function setInstructionalNotes(string $instructionalNotes = null): void
    {
        $this->instructionalNotes = $instructionalNotes;
    }

    public function setSessionType(SessionTypeInterface $sessionType)
    {
        $this->sessionType = $sessionType;
    }

    public function getSessionType(): SessionTypeInterface
    {
        return $this->sessionType;
    }

    public function setCourse(CourseInterface $course)
    {
        $this->course = $course;
    }

    public function getCourse(): ?CourseInterface
    {
        return $this->course;
    }

    public function setIlmSession(IlmSessionInterface $ilmSession = null)
    {
        $this->ilmSession = $ilmSession;
        if ($ilmSession) {
            $ilmSession->setSession($this);
        }
    }

    public function getIlmSession(): ?IlmSessionInterface
    {
        return $this->ilmSession;
    }

    public function setLearningMaterials(Collection $learningMaterials = null)
    {
        $this->learningMaterials = new ArrayCollection();
        if (is_null($learningMaterials)) {
            return;
        }

        foreach ($learningMaterials as $learningMaterial) {
            $this->addLearningMaterial($learningMaterial);
        }
    }

    public function addLearningMaterial(SessionLearningMaterialInterface $learningMaterial)
    {
        if (!$this->learningMaterials->contains($learningMaterial)) {
            $this->learningMaterials->add($learningMaterial);
        }
    }

    public function removeLearningMaterial(SessionLearningMaterialInterface $learningMaterial)
    {
        $this->learningMaterials->removeElement($learningMaterial);
    }

    public function getLearningMaterials(): Collection
    {
        return $this->learningMaterials;
    }

    public function getSchool(): ?SchoolInterface
    {
        if ($course = $this->getCourse()) {
            return $course->getSchool();
        }
        return null;
    }

    public function addAdministrator(UserInterface $administrator)
    {
        if (!$this->administrators->contains($administrator)) {
            $this->administrators->add($administrator);
            $administrator->addAdministeredSession($this);
        }
    }

    public function removeAdministrator(UserInterface $administrator)
    {
        if ($this->administrators->contains($administrator)) {
            $this->administrators->removeElement($administrator);
            $administrator->removeAdministeredSession($this);
        }
    }
    public function addStudentAdvisor(UserInterface $studentAdvisor)
    {
        if (!$this->studentAdvisors->contains($studentAdvisor)) {
            $this->studentAdvisors->add($studentAdvisor);
            $studentAdvisor->addStudentAdvisedSession($this);
        }
    }
    public function removeStudentAdvisor(UserInterface $studentAdvisor)
    {
        if ($this->studentAdvisors->contains($studentAdvisor)) {
            $this->studentAdvisors->removeElement($studentAdvisor);
            $studentAdvisor->removeStudentAdvisedSession($this);
        }
    }

    public function setExcludedSequenceBlocks(Collection $sequenceBlocks)
    {
        $this->sequenceBlocks = new ArrayCollection();

        foreach ($sequenceBlocks as $sequenceBlock) {
            $this->addExcludedSequenceBlock($sequenceBlock);
        }
    }

    public function addExcludedSequenceBlock(CurriculumInventorySequenceBlockInterface $sequenceBlock)
    {
        if (!$this->excludedSequenceBlocks->contains($sequenceBlock)) {
            $this->excludedSequenceBlocks->add($sequenceBlock);
        }
    }

    public function removeExcludedSequenceBlock(CurriculumInventorySequenceBlockInterface $sequenceBlock)
    {
        $this->excludedSequenceBlocks->removeElement($sequenceBlock);
    }

    public function getExcludedSequenceBlocks(): Collection
    {
        return $this->excludedSequenceBlocks;
    }

    public function setPostrequisite(SessionInterface $postrequisite = null)
    {
        $this->postrequisite = $postrequisite;
    }

    public function getPostrequisite(): ?SessionInterface
    {
        return $this->postrequisite;
    }

    public function setPrerequisites(Collection $prerequisites)
    {
        $this->prerequisites = new ArrayCollection();

        foreach ($prerequisites as $prerequisite) {
            $this->addPrerequisite($prerequisite);
        }
    }

    public function addPrerequisite(SessionInterface $prerequisite)
    {
        if (!$this->prerequisites->contains($prerequisite)) {
            $this->prerequisites->add($prerequisite);
            $prerequisite->setPostrequisite($this);
        }
    }

    public function removePrerequisite(SessionInterface $prerequisite)
    {
        $this->prerequisites->removeElement($prerequisite);
    }

    public function getPrerequisites(): Collection
    {
        return $this->prerequisites;
    }

    /**
     * @inheritDoc
     */
    public function getIndexableCourses(): array
    {
        return [$this->course];
    }
}
