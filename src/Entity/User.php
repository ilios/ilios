<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Traits\AlertableEntity;
use App\Traits\CohortsEntity;
use App\Traits\InstructorGroupsEntity;
use App\Traits\LearnerGroupsEntity;
use App\Traits\LearningMaterialsEntity;
use App\Attribute as IA;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;
use App\Traits\OfferingsEntity;
use App\Traits\ProgramYearsEntity;
use App\Traits\SchoolEntity;
use App\Repository\UserRepository;

/**
 * Class User
 */
#[ORM\Table(name: 'user')]
#[ORM\Index(columns: ["school_id"], name: "fkey_user_school")]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[IA\Entity]
class User implements UserInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;
    use OfferingsEntity;
    use ProgramYearsEntity;
    use SchoolEntity;
    use AlertableEntity;
    use LearnerGroupsEntity;
    use CohortsEntity;
    use InstructorGroupsEntity;
    use LearningMaterialsEntity;

    /**
     * @var int
     * @Assert\Type(type="integer")
     */
    #[ORM\Column(name: 'user_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\ReadOnly]
    protected $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 50
     * )
     */
    #[ORM\Column(name: 'last_name', type: 'string', length: 50)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $lastName;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 50
     * )
     */
    #[ORM\Column(name: 'first_name', type: 'string', length: 50)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $firstName;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=20)
     * })
     */
    #[ORM\Column(name: 'middle_name', type: 'string', length: 20, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $middleName;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=200)
     * })
     */
    #[ORM\Column(name: 'display_name', type: 'string', length: 200, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $displayName;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=30)
     * })
     */
    #[ORM\Column(name: 'phone', type: 'string', length: 30, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $phone;

    /**
     * @var string
     * @Assert\Email
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 1,
     *      max = 100
     * )
     */
    #[ORM\Column(name: 'email', type: 'string', length: 100)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $email;

    /**
     * @var string
     * @Assert\Email
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=100)
     * })
     */
    #[ORM\Column(name: 'preferred_email', type: 'string', length: 100, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $preferredEmail;

    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type(type="boolean")
     */
    #[ORM\Column(name: 'added_via_ilios', type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    protected $addedViaIlios;

    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type(type="boolean")
     */
    #[ORM\Column(name: 'enabled', type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    protected $enabled;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=16)
     * })
     */
    #[ORM\Column(name: 'uc_uid', type: 'string', length: 16, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $campusId;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=16)
     * })
     */
    #[ORM\Column(name: 'other_id', type: 'string', length: 16, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $otherId;

    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type(type="boolean")
     */
    #[ORM\Column(name: 'examined', type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    protected $examined;

    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type(type="boolean")
     */
    #[ORM\Column(name: 'user_sync_ignore', type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    protected $userSyncIgnore;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 64,
     *      max = 64
     * )
     */
    #[ORM\Column(name: 'ics_feed_key', type: 'string', length: 64, unique: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $icsFeedKey;

    /**
     * @var AuthenticationInterface
     */
    #[ORM\OneToOne(targetEntity: 'Authentication', mappedBy: 'user')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected $authentication;

    /**
     * @var ArrayCollection|AuditLogInterface[]
     */
    #[ORM\OneToMany(targetEntity: 'AuditLog', mappedBy: 'user')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $auditLogs;

    /**
     * @var ArrayCollection|LearningMaterialInterface[]
     * Don't put learningMaterials in the user API it takes forever to load them all
     */
    #[ORM\OneToMany(mappedBy: 'owningUser', targetEntity: 'LearningMaterial')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Type('entityCollection')]
    protected $learningMaterials;

    /**
     * @var ArrayCollection|ReportInterface[]
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: 'Report')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $reports;

    /**
     * @var SchoolInterface
     */
    #[ORM\ManyToOne(targetEntity: 'School')]
    #[ORM\JoinColumn(name: 'school_id', referencedColumnName: 'school_id', nullable: false)]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected $school;

    /**
     * @var ArrayCollection|CourseInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'Course', mappedBy: 'directors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $directedCourses;

    /**
     * @var ArrayCollection|CourseInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'Course', mappedBy: 'administrators')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $administeredCourses;

    /**
     * @var ArrayCollection|CourseInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'Course', mappedBy: 'studentAdvisors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $studentAdvisedCourses;

    /**
     * @var ArrayCollection|SessionInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'Session', mappedBy: 'administrators')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $administeredSessions;

    /**
     * @var ArrayCollection|SessionInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'Session', mappedBy: 'studentAdvisors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $studentAdvisedSessions;

    /**
     * @var ArrayCollection|LearnerGroupInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'LearnerGroup', mappedBy: 'users')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $learnerGroups;

    /**
     * @var ArrayCollection|LearnerGroupInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'LearnerGroup', mappedBy: 'instructors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $instructedLearnerGroups;

    /**
     * @var ArrayCollection|InstructorGroupInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'InstructorGroup', mappedBy: 'users')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $instructorGroups;

    /**
     * @var ArrayCollection|IlmSessionInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'IlmSession', mappedBy: 'instructors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $instructorIlmSessions;

    /**
     * @var ArrayCollection|IlmSessionInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'IlmSession', mappedBy: 'learners')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $learnerIlmSessions;

    /**
     * @var ArrayCollection|OfferingInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'Offering', mappedBy: 'learners')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $offerings;

    /**
     * @var ArrayCollection|OfferingInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'Offering', mappedBy: 'instructors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $instructedOfferings;

    /**
     * @var ArrayCollection|ProgramYearInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'ProgramYear', mappedBy: 'directors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $programYears;

    /**
     * @var ArrayCollection|AlertInterface[]
     * Don't put alerts in the user API it takes forever to load them all
     */
    #[ORM\ManyToMany(targetEntity: 'Alert', mappedBy: 'instigators')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Type('entityCollection')]
    protected $alerts;

    /**
     * @var Collection
     */
    #[ORM\ManyToMany(targetEntity: 'UserRole', inversedBy: 'users')]
    #[ORM\JoinTable(name: 'user_x_user_role')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_role_id', referencedColumnName: 'user_role_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $roles;

    /**
     * @var Collection
     */
    #[ORM\ManyToMany(targetEntity: 'Cohort', inversedBy: 'users')]
    #[ORM\JoinTable(name: 'user_x_cohort')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\InverseJoinColumn(name: 'cohort_id', referencedColumnName: 'cohort_id')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $cohorts;

    /**
     * @var CohortInterface
     */
    #[ORM\ManyToOne(targetEntity: 'Cohort')]
    #[ORM\JoinColumn(name: 'primary_cohort_id', referencedColumnName: 'cohort_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected $primaryCohort;

    /**
     * @var ArrayCollection|PendingUserUpdateInterface[]
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: 'PendingUserUpdate')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $pendingUserUpdates;

    /**
     * @var ArrayCollection|SchoolInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'School', mappedBy: 'directors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $directedSchools;

    /**
     * @var ArrayCollection|SchoolInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'School', mappedBy: 'administrators')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $administeredSchools;

    /**
     * @var ArrayCollection|ProgramInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'Program', mappedBy: 'directors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $directedPrograms;

    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type(type="boolean")
     */
    #[ORM\Column(name: 'root', type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    protected $root;

    /**
     * @var ArrayCollection|CurriculumInventoryReportInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'CurriculumInventoryReport', mappedBy: 'administrators')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $administeredCurriculumInventoryReports;

    public function __construct()
    {
        $this->directedCourses = new ArrayCollection();
        $this->learnerGroups = new ArrayCollection();
        $this->instructedLearnerGroups = new ArrayCollection();
        $this->instructorGroups = new ArrayCollection();
        $this->offerings = new ArrayCollection();
        $this->instructedOfferings = new ArrayCollection();
        $this->instructorIlmSessions = new ArrayCollection();
        $this->programYears = new ArrayCollection();
        $this->alerts = new ArrayCollection();
        $this->roles = new ArrayCollection();
        $this->learningMaterials = new ArrayCollection();
        $this->reports = new ArrayCollection();
        $this->cohorts = new ArrayCollection();
        $this->pendingUserUpdates = new ArrayCollection();
        $this->auditLogs = new ArrayCollection();
        $this->administeredSessions = new ArrayCollection();
        $this->administeredCourses = new ArrayCollection();
        $this->studentAdvisedCourses = new ArrayCollection();
        $this->studentAdvisedSessions = new ArrayCollection();
        $this->learnerIlmSessions = new ArrayCollection();
        $this->directedSchools = new ArrayCollection();
        $this->administeredSchools = new ArrayCollection();
        $this->directedPrograms = new ArrayCollection();
        $this->administeredCurriculumInventoryReports = new ArrayCollection();
        $this->addedViaIlios = false;
        $this->enabled = true;
        $this->examined = false;
        $this->userSyncIgnore = false;
        $this->root = false;

        $this->generateIcsFeedKey();
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $middleName
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;
    }

    public function getMiddleName(): string
    {
        return $this->middleName;
    }

    public function getFirstAndLastName(): string
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setPreferredEmail($email)
    {
        $this->preferredEmail = $email;
    }

    public function getPreferredEmail(): string
    {
        return $this->preferredEmail;
    }

    /**
     * @param bool $addedViaIlios
     */
    public function setAddedViaIlios($addedViaIlios)
    {
        $this->addedViaIlios = $addedViaIlios;
    }

    public function isAddedViaIlios(): bool
    {
        return $this->addedViaIlios;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param string $campusId
     */
    public function setCampusId($campusId)
    {
        $this->campusId = $campusId;
    }

    public function getCampusId(): string
    {
        return $this->campusId;
    }

    /**
     * @param string $otherId
     */
    public function setOtherId($otherId)
    {
        $this->otherId = $otherId;
    }

    public function getOtherId(): string
    {
        return $this->otherId;
    }

    /**
     * @param bool $examined
     */
    public function setExamined($examined)
    {
        $this->examined = $examined;
    }

    public function isExamined(): bool
    {
        return $this->examined;
    }

    /**
     * @param bool $userSyncIgnore
     */
    public function setUserSyncIgnore($userSyncIgnore)
    {
        $this->userSyncIgnore = $userSyncIgnore;
    }

    public function isUserSyncIgnore(): bool
    {
        return $this->userSyncIgnore;
    }

    public function generateIcsFeedKey()
    {
        $random = random_bytes(128);

        // and current time to give some more uniqueness
        $key = microtime() . '_' . $random;

        // hash the string to give consistent length and URL safe characters
        $this->icsFeedKey = hash('sha256', $key);
    }

    public function setIcsFeedKey($icsFeedKey)
    {
        $this->icsFeedKey = $icsFeedKey;
    }

    public function getIcsFeedKey(): string
    {
        return $this->icsFeedKey;
    }

    public function setDirectedCourses(Collection $courses)
    {
        $this->directedCourses = new ArrayCollection();

        foreach ($courses as $course) {
            $this->addDirectedCourse($course);
        }
    }

    public function addDirectedCourse(CourseInterface $course)
    {
        if (!$this->directedCourses->contains($course)) {
            $this->directedCourses->add($course);
            $course->addDirector($this);
        }
    }

    public function removeDirectedCourse(CourseInterface $course)
    {
        $this->directedCourses->removeElement($course);
        $course->removeDirector($this);
    }

    public function getDirectedCourses(): Collection
    {
        return $this->directedCourses;
    }

    public function setAdministeredCourses(Collection $courses)
    {
        $this->administeredCourses = new ArrayCollection();

        foreach ($courses as $course) {
            $this->addAdministeredCourse($course);
        }
    }

    public function addAdministeredCourse(CourseInterface $course)
    {
        if (!$this->administeredCourses->contains($course)) {
            $this->administeredCourses->add($course);
            $course->addAdministrator($this);
        }
    }

    public function removeAdministeredCourse(CourseInterface $course)
    {
        $this->administeredCourses->removeElement($course);
        $course->removeAdministrator($this);
    }

    public function getAdministeredCourses(): Collection
    {
        return $this->administeredCourses;
    }

    public function setStudentAdvisedCourses(Collection $courses)
    {
        $this->studentAdvisedCourses = new ArrayCollection();

        foreach ($courses as $course) {
            $this->addStudentAdvisedCourse($course);
        }
    }

    public function addStudentAdvisedCourse(CourseInterface $course)
    {
        if (!$this->studentAdvisedCourses->contains($course)) {
            $this->studentAdvisedCourses->add($course);
            $course->addStudentAdvisor($this);
        }
    }

    public function removeStudentAdvisedCourse(CourseInterface $course)
    {
        $this->studentAdvisedCourses->removeElement($course);
        $course->removeStudentAdvisor($this);
    }

    public function getStudentAdvisedCourses(): Collection
    {
        return $this->studentAdvisedCourses;
    }

    public function setAdministeredSessions(Collection $sessions)
    {
        $this->administeredSessions = new ArrayCollection();

        foreach ($sessions as $session) {
            $this->addAdministeredSession($session);
        }
    }

    public function addAdministeredSession(SessionInterface $session)
    {
        if (!$this->administeredSessions->contains($session)) {
            $this->administeredSessions->add($session);
            $session->addAdministrator($this);
        }
    }

    public function removeAdministeredSession(SessionInterface $session)
    {
        $this->administeredSessions->removeElement($session);
        $session->removeAdministrator($this);
    }

    public function getAdministeredSessions(): Collection
    {
        return $this->administeredSessions;
    }

    public function setStudentAdvisedSessions(Collection $sessions)
    {
        $this->studentAdvisedSessions = new ArrayCollection();

        foreach ($sessions as $session) {
            $this->addStudentAdvisedSession($session);
        }
    }

    public function addStudentAdvisedSession(SessionInterface $session)
    {
        if (!$this->studentAdvisedSessions->contains($session)) {
            $this->studentAdvisedSessions->add($session);
            $session->addStudentAdvisor($this);
        }
    }

    public function removeStudentAdvisedSession(SessionInterface $session)
    {
        $this->studentAdvisedSessions->removeElement($session);
        $session->removeStudentAdvisor($this);
    }

    public function getStudentAdvisedSessions(): Collection
    {
        return $this->studentAdvisedSessions;
    }

    public function isDirectingCourse($courseId): bool
    {
        return $this->directedCourses->map(fn(CourseInterface $course) => $course->getId())->contains($courseId);
    }

    public function addLearnerGroup(LearnerGroupInterface $learnerGroup)
    {
        if (!$this->learnerGroups->contains($learnerGroup)) {
            $this->learnerGroups->add($learnerGroup);
            $learnerGroup->addUser($this);
        }
    }

    public function removeLearnerGroup(LearnerGroupInterface $learnerGroup)
    {
        if ($this->learnerGroups->contains($learnerGroup)) {
            $this->learnerGroups->removeElement($learnerGroup);
            $learnerGroup->removeUser($this);
        }
    }

    public function setInstructedLearnerGroups(Collection $instructedLearnerGroups)
    {
        $this->instructedLearnerGroups = new ArrayCollection();

        foreach ($instructedLearnerGroups as $instructedLearnerGroup) {
            $this->addInstructedLearnerGroup($instructedLearnerGroup);
        }
    }

    public function addInstructedLearnerGroup(LearnerGroupInterface $instructedLearnerGroup)
    {
        if (!$this->instructedLearnerGroups->contains($instructedLearnerGroup)) {
            $this->instructedLearnerGroups->add($instructedLearnerGroup);
            $instructedLearnerGroup->addInstructor($this);
        }
    }

    public function removeInstructedLearnerGroup(LearnerGroupInterface $instructedLearnerGroup)
    {
        $this->instructedLearnerGroups->removeElement($instructedLearnerGroup);
        $instructedLearnerGroup->removeInstructor($this);
    }

    public function getInstructedLearnerGroups(): Collection
    {
        return $this->instructedLearnerGroups;
    }

    public function addInstructorGroup(InstructorGroupInterface $instructorGroup)
    {
        if (!$this->instructorGroups->contains($instructorGroup)) {
            $this->instructorGroups->add($instructorGroup);
            $instructorGroup->addUser($this);
        }
    }

    public function removeInstructorGroup(InstructorGroupInterface $instructorGroup)
    {
        $this->instructorGroups->removeElement($instructorGroup);
        $instructorGroup->removeUser($this);
    }

    public function setInstructorIlmSessions(Collection $sessions)
    {
        $this->instructorIlmSessions = new ArrayCollection();

        foreach ($sessions as $session) {
            $this->addInstructorIlmSession($session);
        }
    }

    public function addInstructorIlmSession(IlmSessionInterface $session)
    {
        if (!$this->instructorIlmSessions->contains($session)) {
            $this->instructorIlmSessions->add($session);
            $session->addInstructor($this);
        }
    }

    public function removeInstructorIlmSession(IlmSessionInterface $session)
    {
        $this->instructorIlmSessions->removeElement($session);
        $session->removeInstructor($this);
    }

    public function getInstructorIlmSessions()
    {
        return $this->instructorIlmSessions;
    }

    public function setLearnerIlmSessions(Collection $sessions)
    {
        $this->learnerIlmSessions = new ArrayCollection();

        foreach ($sessions as $session) {
            $this->addLearnerIlmSession($session);
        }
    }

    public function addLearnerIlmSession(IlmSessionInterface $session)
    {
        if (!$this->learnerIlmSessions->contains($session)) {
            $this->learnerIlmSessions->add($session);
            $session->addLearner($this);
        }
    }

    public function removeLearnerIlmSession(IlmSessionInterface $session)
    {
        if ($this->learnerIlmSessions->contains($session)) {
            $this->learnerIlmSessions->removeElement($session);
            $session->removeLearner($this);
        }
    }

    public function getLearnerIlmSessions(): Collection
    {
        return $this->learnerIlmSessions;
    }

    public function addAlert(AlertInterface $alert)
    {
        if (!$this->alerts->contains($alert)) {
            $this->alerts->add($alert);
            $alert->addInstigator($this);
        }
    }

    public function removeAlert(AlertInterface $alert)
    {
        $this->alerts->removeElement($alert);
        $alert->removeInstigator($this);
    }

    public function setRoles(Collection $roles)
    {
        $this->roles = new ArrayCollection();

        foreach ($roles as $role) {
            $this->addRole($role);
        }
    }

    public function addRole(UserRoleInterface $role)
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }
    }

    public function removeRole(UserRoleInterface $role)
    {
        $this->roles->removeElement($role);
    }

    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function setReports(Collection $reports)
    {
        $this->reports = new ArrayCollection();

        foreach ($reports as $report) {
            $this->addReport($report);
        }
    }

    public function addReport(ReportInterface $report)
    {
        if (!$this->reports->contains($report)) {
            $this->reports->add($report);
        }
    }

    public function removeReport(ReportInterface $report)
    {
        $this->reports->removeElement($report);
    }

    public function getReports(): Collection
    {
        return $this->reports;
    }

    /**
     * Remove Primary cohort if it is no longer a cohorts
     * @inheritdoc
     */
    public function setCohorts(Collection $cohorts)
    {
        $this->cohorts = new ArrayCollection();
        foreach ($cohorts as $cohort) {
            $this->addCohort($cohort);
        }
        if (!$cohorts->contains($this->getPrimaryCohort())) {
            $this->setPrimaryCohort(null);
        }
    }

    /**
     * Remove Primary cohort if it is no longer a cohorts
     * @inheritdoc
     */
    public function removeCohort(CohortInterface $cohort)
    {
        $this->cohorts->removeElement($cohort);
        $primaryCohort = $this->getPrimaryCohort();
        if ($primaryCohort && $cohort === $primaryCohort) {
            $this->setPrimaryCohort(null);
        }
    }

    public function setPrimaryCohort(CohortInterface $primaryCohort = null)
    {
        if ($primaryCohort && !$this->getCohorts()->contains($primaryCohort)) {
            $this->addCohort($primaryCohort);
        }
        $this->primaryCohort = $primaryCohort;
    }

    public function getPrimaryCohort(): ?CohortInterface
    {
        return $this->primaryCohort;
    }

    public function setInstructedOfferings(Collection $instructedOfferings)
    {
        $this->instructedOfferings = new ArrayCollection();

        foreach ($instructedOfferings as $instructedOffering) {
            $this->addInstructedOffering($instructedOffering);
        }
    }

    public function addInstructedOffering(Offering $instructedOffering)
    {
        if (!$this->instructedOfferings->contains($instructedOffering)) {
            $this->instructedOfferings->add($instructedOffering);
            $instructedOffering->addInstructor($this);
        }
    }

    public function removeInstructedOffering(Offering $instructedOffering)
    {
        $this->instructedOfferings->removeElement($instructedOffering);
        $instructedOffering->removeInstructor($this);
    }

    public function getInstructedOfferings()
    {
        return $this->instructedOfferings;
    }

    public function setAuthentication(AuthenticationInterface $authentication = null)
    {
        $this->authentication = $authentication;

        if ($authentication) {
            $authentication->setUser($this);
        }
    }

    public function getAuthentication(): ?AuthenticationInterface
    {
        return $this->authentication;
    }

    public function setAuditLogs(Collection $auditLogs)
    {
        $this->auditLogs = new ArrayCollection();

        foreach ($auditLogs as $auditLog) {
            $this->addAuditLog($auditLog);
        }
    }

    public function addAuditLog(AuditLogInterface $auditLog)
    {
        if (!$this->auditLogs->contains($auditLog)) {
            $this->auditLogs->add($auditLog);
        }
    }

    public function removeAuditLog(AuditLogInterface $auditLog)
    {
        $this->auditLogs->removeElement($auditLog);
    }

    public function getAuditLogs(): Collection
    {
        return $this->auditLogs;
    }

    public function setPendingUserUpdates(Collection $pendingUserUpdates)
    {
        $this->pendingUserUpdates = new ArrayCollection();

        foreach ($pendingUserUpdates as $pendingUserUpdate) {
            $this->addPendingUserUpdate($pendingUserUpdate);
        }
    }

    public function addPendingUserUpdate(PendingUserUpdateInterface $pendingUserUpdate)
    {
        if (!$this->pendingUserUpdates->contains($pendingUserUpdate)) {
            $this->pendingUserUpdates->add($pendingUserUpdate);
        }
    }

    public function removePendingUserUpdate(PendingUserUpdateInterface $pendingUserUpdate)
    {
        $this->pendingUserUpdates->removeElement($pendingUserUpdate);
    }

    public function getPendingUserUpdates(): Collection
    {
        return $this->pendingUserUpdates;
    }

    public function addProgramYear(ProgramYearInterface $programYear)
    {
        if (!$this->programYears->contains($programYear)) {
            $this->programYears->add($programYear);
            $programYear->addDirector($this);
        }
    }

    public function removeProgramYear(ProgramYearInterface $programYear)
    {
        if ($this->programYears->contains($programYear)) {
            $this->programYears->removeElement($programYear);
            $programYear->removeDirector($this);
        }
    }

    public function addOffering(OfferingInterface $offering)
    {
        if (!$this->offerings->contains($offering)) {
            $this->offerings->add($offering);
            $offering->addLearner($this);
        }
    }

    public function removeOffering(OfferingInterface $offering)
    {
        if ($this->offerings->contains($offering)) {
            $this->offerings->removeElement($offering);
            $offering->removeLearner($this);
        }
    }

    public function setDirectedSchools(Collection $schools)
    {
        $this->directedSchools = new ArrayCollection();

        foreach ($schools as $school) {
            $this->addDirectedSchool($school);
        }
    }

    public function addDirectedSchool(SchoolInterface $school)
    {
        if (!$this->directedSchools->contains($school)) {
            $this->directedSchools->add($school);
            $school->addDirector($this);
        }
    }

    public function removeDirectedSchool(SchoolInterface $school)
    {
        $this->directedSchools->removeElement($school);
        $school->removeDirector($this);
    }

    public function getDirectedSchools(): Collection
    {
        return $this->directedSchools;
    }

    public function setAdministeredSchools(Collection $schools)
    {
        $this->administeredSchools = new ArrayCollection();

        foreach ($schools as $school) {
            $this->addAdministeredSchool($school);
        }
    }

    public function addAdministeredSchool(SchoolInterface $school)
    {
        if (!$this->administeredSchools->contains($school)) {
            $this->administeredSchools->add($school);
            $school->addAdministrator($this);
        }
    }

    public function removeAdministeredSchool(SchoolInterface $school)
    {
        $this->administeredSchools->removeElement($school);
        $school->removeAdministrator($this);
    }

    public function getAdministeredSchools(): Collection
    {
        return $this->administeredSchools;
    }

    public function setDirectedPrograms(Collection $programs)
    {
        $this->directedPrograms = new ArrayCollection();

        foreach ($programs as $program) {
            $this->addDirectedProgram($program);
        }
    }

    public function addDirectedProgram(ProgramInterface $program)
    {
        if (!$this->directedPrograms->contains($program)) {
            $this->directedPrograms->add($program);
            $program->addDirector($this);
        }
    }

    public function removeDirectedProgram(ProgramInterface $program)
    {
        $this->directedPrograms->removeElement($program);
        $program->removeDirector($this);
    }

    public function getDirectedPrograms(): Collection
    {
        return $this->directedPrograms;
    }

    /**
     * Get all the schools an user is affiliated with so we can match
     * permissions.
     */
    public function getAllSchools(): Collection
    {
        $cohortSchools = $this->getCohorts()->map(fn(CohortInterface $cohort) => $cohort->getSchool());

        $directedCourseSchools = $this->getDirectedCourses()->map(fn(CourseInterface $course) => $course->getSchool());

        $learnerGroupSchools = $this->getLearnerGroups()->map(fn(LearnerGroupInterface $lg) => $lg->getSchool());

        $instructedLgSchools = $this->getInstructedLearnerGroups()->map(
            fn(LearnerGroupInterface $lg) => $lg->getSchool()
        );

        $instGroupSchools = $this->getInstructorGroups()->map(fn(InstructorGroupInterface $ig) => $ig->getSchool());

        $insIlmSchools = $this->getInstructorIlmSessions()->map(fn(IlmSessionInterface $ilm) => $ilm->getSchool());

        $allSchools = array_merge(
            $cohortSchools->toArray(),
            $directedCourseSchools->toArray(),
            $learnerGroupSchools->toArray(),
            $instructedLgSchools->toArray(),
            $instGroupSchools->toArray(),
            $insIlmSchools->toArray()
        );
        $allSchools[] = $this->getSchool();
        $allSchools = array_unique($allSchools);
        $allSchools = array_filter($allSchools);

        return new ArrayCollection($allSchools);
    }

    public function isRoot(): bool
    {
        return $this->root;
    }

    public function setRoot($root)
    {
        $this->root = $root;
    }

    public function getAdministeredCurriculumInventoryReports(): Collection
    {
        return $this->administeredCurriculumInventoryReports;
    }

    public function setAdministeredCurriculumInventoryReports(Collection $reports)
    {
        $this->administeredCurriculumInventoryReports = new ArrayCollection();

        foreach ($reports as $report) {
            $this->addAdministeredCurriculumInventoryReport($report);
        }
    }

    public function addAdministeredCurriculumInventoryReport(CurriculumInventoryReportInterface $report)
    {
        if (!$this->administeredCurriculumInventoryReports->contains($report)) {
            $this->administeredCurriculumInventoryReports->add($report);
            $report->addAdministrator($this);
        }
    }

    public function removeAdministeredCurriculumInventoryReport(CurriculumInventoryReportInterface $report)
    {
        $this->administeredCurriculumInventoryReports->removeElement($report);
        $report->removeAdministrator($this);
    }
}
