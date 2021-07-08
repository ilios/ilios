<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\SessionObjectivesEntity;
use App\Traits\StudentAdvisorsEntity;
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
use App\Annotation as IS;
use App\Traits\TitledEntity;
use App\Traits\StringableIdEntity;
use App\Traits\TimestampableEntity;
use App\Traits\OfferingsEntity;
use App\Traits\IdentifiableEntity;
use App\Repository\SessionRepository;

/**
 * Class Session
 *   indexes={
 *   }
 * )
 * @IS\Entity
 */
#[ORM\Table(name: 'session')]
#[ORM\Index(name: 'session_type_id_k', columns: ['session_type_id'])]
#[ORM\Index(name: 'course_id_k', columns: ['course_id'])]
#[ORM\Index(name: 'session_course_type_title_k', columns: ['session_id', 'course_id', 'session_type_id', 'title'])]
#[ORM\Entity(repositoryClass: SessionRepository::class)]
class Session implements SessionInterface
{
    use IdentifiableEntity;
    use TitledEntity;
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

    /**
     * @var int
     * @Assert\Type(type="integer")
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    #[ORM\Column(name: 'session_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected $id;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 200
     * )
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(type: 'string', length: 200, nullable: true)]
    protected $title;

    /**
     * @var bool
     * @Assert\Type(type="bool")
     * @IS\Expose
     * @IS\Type("boolean")
     */
    #[ORM\Column(name: 'attire_required', type: 'boolean', nullable: true)]
    protected $attireRequired;

    /**
     * @var bool
     * @Assert\Type(type="bool")
     * @IS\Expose
     * @IS\Type("boolean")
     */
    #[ORM\Column(name: 'equipment_required', type: 'boolean', nullable: true)]
    protected $equipmentRequired;

    /**
     * @var bool
     * @Assert\Type(type="bool")
     * @IS\Expose
     * @IS\Type("boolean")
     */
    #[ORM\Column(name: 'supplemental', type: 'boolean', nullable: true)]
    protected $supplemental;

    /**
     * @var bool
     * @Assert\Type(type="bool")
     * @IS\Expose
     * @IS\Type("boolean")
     */
    #[ORM\Column(name: 'attendance_required', type: 'boolean', nullable: true)]
    protected $attendanceRequired;

    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @IS\Expose
     * @IS\Type("boolean")
     */
    #[ORM\Column(name: 'published_as_tbd', type: 'boolean')]
    protected $publishedAsTbd;

    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @IS\Expose
     * @IS\Type("boolean")
     */
    #[ORM\Column(type: 'boolean')]
    protected $published;

    /**
     * @var DateTime
     * @Assert\NotBlank()
     * @IS\Expose
     * @IS\ReadOnly
     * @IS\Type("dateTime")
     */
    #[ORM\Column(name: 'last_updated_on', type: 'datetime')]
    protected $updatedAt;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=65000)
     * })
     * @IS\Expose
     * @IS\Type("string")
     * @IS\RemoveMarkup
     */
    #[ORM\Column(name: 'instructionalNotes', type: 'text', nullable: true)]
    protected $instructionalNotes;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=65000)
     * })
     * @IS\Expose
     * @IS\Type("string")
     * @IS\RemoveMarkup
     */
    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    protected $description;

    /**
     * @var SessionTypeInterface
     * @Assert\NotNull()
     * })
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\ManyToOne(targetEntity: 'SessionType', inversedBy: 'sessions')]
    #[ORM\JoinColumn(name: 'session_type_id', referencedColumnName: 'session_type_id', nullable: false)]
    protected $sessionType;

    /**
     * @var CourseInterface
     * @Assert\NotNull()
     * })
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\ManyToOne(targetEntity: 'Course', inversedBy: 'sessions')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'course_id', nullable: false, onDelete: 'CASCADE')]
    protected $course;

    /**
     * @var IlmSessionInterface
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\OneToOne(targetEntity: 'IlmSession', mappedBy: 'session')]
    protected $ilmSession;

    /**
     * @var ArrayCollection|TermInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'Term', inversedBy: 'sessions')]
    #[ORM\JoinTable(name: 'session_x_term')]
    #[ORM\JoinColumn(name: 'session_id', referencedColumnName: 'session_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'term_id', referencedColumnName: 'term_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $terms;

    /**
     * @var ArrayCollection|SessionObjectiveInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(targetEntity: 'SessionObjective', mappedBy: 'session')]
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
    protected $sessionObjectives;

    /**
     * @var ArrayCollection|MeshDescriptorInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'MeshDescriptor', inversedBy: 'sessions')]
    #[ORM\JoinTable(name: 'session_x_mesh')]
    #[ORM\JoinColumn(name: 'session_id', referencedColumnName: 'session_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'mesh_descriptor_uid', referencedColumnName: 'mesh_descriptor_uid', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $meshDescriptors;

    /**
     * @var ArrayCollection|SessionLearningMaterialInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(targetEntity: 'SessionLearningMaterial', mappedBy: 'session')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $learningMaterials;

    /**
     * @var ArrayCollection|OfferingInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(targetEntity: 'Offering', mappedBy: 'session')]
    #[ORM\OrderBy(['id' => 'ASC'])]
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
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'administeredSessions')]
    #[ORM\JoinTable(name: 'session_administrator')]
    #[ORM\JoinColumn(name: 'session_id', referencedColumnName: 'session_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $administrators;

    /**
     * @var ArrayCollection|UserInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'studentAdvisedSessions')]
    #[ORM\JoinTable(name: 'session_student_advisor')]
    #[ORM\JoinColumn(name: 'session_id', referencedColumnName: 'session_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $studentAdvisors;

    /**
     * @var SessionInterface
     * })
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\ManyToOne(targetEntity: 'Session', inversedBy: 'prerequisites')]
    #[ORM\JoinColumn(name: 'postrequisite_id', referencedColumnName: 'session_id', onDelete: 'SET NULL')]
    protected $postrequisite;

    /**
     * @var SessionInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(targetEntity: 'Session', mappedBy: 'postrequisite')]
    #[ORM\OrderBy(['id' => 'ASC'])]
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

    /**
     * @return bool
     */
    public function isAttireRequired()
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

    /**
     * @return bool
     */
    public function isEquipmentRequired()
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

    /**
     * @return bool
     */
    public function isSupplemental()
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

    /**
     * @return bool
     */
    public function isAttendanceRequired()
    {
        return $this->attendanceRequired;
    }

    /**
     * @inheritdoc
     */
    public function getInstructionalNotes(): ?string
    {
        return $this->instructionalNotes;
    }

    /**
     * @inheritdoc
     */
    public function setInstructionalNotes(string $instructionalNotes = null): void
    {
        $this->instructionalNotes = $instructionalNotes;
    }

    /**
     * @param SessionTypeInterface $sessionType
     */
    public function setSessionType(SessionTypeInterface $sessionType)
    {
        $this->sessionType = $sessionType;
    }

    /**
     * @return SessionTypeInterface
     */
    public function getSessionType()
    {
        return $this->sessionType;
    }

    /**
     * @param CourseInterface $course
     */
    public function setCourse(CourseInterface $course)
    {
        $this->course = $course;
    }

    /**
     * @inheritdoc
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * @param IlmSessionInterface $ilmSession
     */
    public function setIlmSession(IlmSessionInterface $ilmSession = null)
    {
        $this->ilmSession = $ilmSession;
        if ($ilmSession) {
            $ilmSession->setSession($this);
        }
    }

    /**
     * @return IlmSessionInterface
     */
    public function getIlmSession()
    {
        return $this->ilmSession;
    }

    /**
     * @param Collection $learningMaterials
     */
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

    /**
     * @param SessionLearningMaterialInterface $learningMaterial
     */
    public function addLearningMaterial(SessionLearningMaterialInterface $learningMaterial)
    {
        if (!$this->learningMaterials->contains($learningMaterial)) {
            $this->learningMaterials->add($learningMaterial);
        }
    }

    /**
     * @param SessionLearningMaterialInterface $learningMaterial
     */
    public function removeLearningMaterial(SessionLearningMaterialInterface $learningMaterial)
    {
        $this->learningMaterials->removeElement($learningMaterial);
    }

    /**
     * @return ArrayCollection|SessionLearningMaterialInterface[]
     */
    public function getLearningMaterials()
    {
        return $this->learningMaterials;
    }

    /**
     * @inheritdoc
     */
    public function getSchool()
    {
        if ($course = $this->getCourse()) {
            return $course->getSchool();
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function addAdministrator(UserInterface $administrator)
    {
        if (!$this->administrators->contains($administrator)) {
            $this->administrators->add($administrator);
            $administrator->addAdministeredSession($this);
        }
    }

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
    public function setExcludedSequenceBlocks(Collection $sequenceBlocks)
    {
        $this->sequenceBlocks = new ArrayCollection();

        foreach ($sequenceBlocks as $sequenceBlock) {
            $this->addExcludedSequenceBlock($sequenceBlock);
        }
    }

    /**
     * @inheritdoc
     */
    public function addExcludedSequenceBlock(CurriculumInventorySequenceBlockInterface $sequenceBlock)
    {
        if (!$this->excludedSequenceBlocks->contains($sequenceBlock)) {
            $this->excludedSequenceBlocks->add($sequenceBlock);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeExcludedSequenceBlock(CurriculumInventorySequenceBlockInterface $sequenceBlock)
    {
        $this->excludedSequenceBlocks->removeElement($sequenceBlock);
    }

    /**
     * @inheritdoc
     */
    public function getExcludedSequenceBlocks()
    {
        return $this->excludedSequenceBlocks;
    }

    /**
     * @inheritdoc
     */
    public function setPostrequisite(SessionInterface $postrequisite = null)
    {
        $this->postrequisite = $postrequisite;
    }

    /**
     * @inheritdoc
     */
    public function getPostrequisite()
    {
        return $this->postrequisite;
    }

    /**
     * @inheritdoc
     */
    public function setPrerequisites(Collection $prerequisites)
    {
        $this->prerequisites = new ArrayCollection();

        foreach ($prerequisites as $prerequisite) {
            $this->addPrerequisite($prerequisite);
        }
    }

    /**
     * @inheritdoc
     */
    public function addPrerequisite(SessionInterface $prerequisite)
    {
        if (!$this->prerequisites->contains($prerequisite)) {
            $this->prerequisites->add($prerequisite);
            $prerequisite->setPostrequisite($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removePrerequisite(SessionInterface $prerequisite)
    {
        $this->prerequisites->removeElement($prerequisite);
    }

    /**
     * @inheritdoc
     */
    public function getPrerequisites()
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

    /**
     * @inheritdoc
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @inheritdoc
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }
}
