<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Traits\AlertableEntity;
use App\Traits\CohortsEntity;
use App\Traits\InstructorGroupsEntity;
use App\Traits\LearnerGroupsEntity;
use App\Traits\LearningMaterialsEntity;
use App\Annotation as IS;
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
 * @IS\Entity
 */
#[ORM\Table(name: 'user')]
#[ORM\Index(columns: ["school_id"], name: "fkey_user_school")]
#[ORM\Entity(repositoryClass: UserRepository::class)]
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
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    #[ORM\Column(name: 'user_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 50
     * )
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'last_name', type: 'string', length: 50)]
    protected $lastName;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 50
     * )
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'first_name', type: 'string', length: 50)]
    protected $firstName;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=20)
     * })
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'middle_name', type: 'string', length: 20, nullable: true)]
    protected $middleName;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=200)
     * })
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'display_name', type: 'string', length: 200, nullable: true)]
    protected $displayName;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=30)
     * })
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'phone', type: 'string', length: 30, nullable: true)]
    protected $phone;

    /**
     * @var string
     * @Assert\Email
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 1,
     *      max = 100
     * )
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'email', type: 'string', length: 100)]
    protected $email;

    /**
     * @var string
     * @Assert\Email
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=100)
     * })
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'preferred_email', type: 'string', length: 100, nullable: true)]
    protected $preferredEmail;

    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type(type="boolean")
     * @IS\Expose
     * @IS\Type("boolean")
     */
    #[ORM\Column(name: 'added_via_ilios', type: 'boolean')]
    protected $addedViaIlios;

    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type(type="boolean")
     * @IS\Expose
     * @IS\Type("boolean")
     */
    #[ORM\Column(name: 'enabled', type: 'boolean')]
    protected $enabled;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=16)
     * })
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'uc_uid', type: 'string', length: 16, nullable: true)]
    protected $campusId;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=16)
     * })
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'other_id', type: 'string', length: 16, nullable: true)]
    protected $otherId;

    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type(type="boolean")
     * @IS\Expose
     * @IS\Type("boolean")
     */
    #[ORM\Column(name: 'examined', type: 'boolean')]
    protected $examined;

    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type(type="boolean")
     * @IS\Expose
     * @IS\Type("boolean")
     */
    #[ORM\Column(name: 'user_sync_ignore', type: 'boolean')]
    protected $userSyncIgnore;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 64,
     *      max = 64
     * )
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'ics_feed_key', type: 'string', length: 64, unique: true)]
    protected $icsFeedKey;

    /**
     * @var AuthenticationInterface
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\OneToOne(targetEntity: 'Authentication', mappedBy: 'user')]
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
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(mappedBy: 'owningUser', targetEntity: 'LearningMaterial')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $learningMaterials;

    /**
     * @var ArrayCollection|ReportInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: 'Report')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $reports;

    /**
     * @var SchoolInterface
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\ManyToOne(targetEntity: 'School')]
    #[ORM\JoinColumn(name: 'school_id', referencedColumnName: 'school_id', nullable: false)]
    protected $school;

    /**
     * @var ArrayCollection|CourseInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'Course', mappedBy: 'directors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $directedCourses;

    /**
     * @var ArrayCollection|CourseInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'Course', mappedBy: 'administrators')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $administeredCourses;

    /**
     * @var ArrayCollection|CourseInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'Course', mappedBy: 'studentAdvisors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $studentAdvisedCourses;

    /**
     * @var ArrayCollection|SessionInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'Session', mappedBy: 'administrators')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $administeredSessions;

    /**
     * @var ArrayCollection|SessionInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'Session', mappedBy: 'studentAdvisors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $studentAdvisedSessions;

    /**
     * @var ArrayCollection|LearnerGroupInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'LearnerGroup', mappedBy: 'users')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $learnerGroups;

    /**
     * @var ArrayCollection|LearnerGroupInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'LearnerGroup', mappedBy: 'instructors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $instructedLearnerGroups;

    /**
     * @var ArrayCollection|InstructorGroupInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'InstructorGroup', mappedBy: 'users')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $instructorGroups;

    /**
     * @var ArrayCollection|IlmSessionInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'IlmSession', mappedBy: 'instructors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $instructorIlmSessions;

    /**
     * @var ArrayCollection|IlmSessionInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'IlmSession', mappedBy: 'learners')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $learnerIlmSessions;

    /**
     * @var ArrayCollection|OfferingInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'Offering', mappedBy: 'learners')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $offerings;

    /**
     * @var ArrayCollection|OfferingInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'Offering', mappedBy: 'instructors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $instructedOfferings;

    /**
     * @var ArrayCollection|ProgramYearInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'ProgramYear', mappedBy: 'directors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $programYears;

    /**
     * @var ArrayCollection|AlertInterface[]
     * Don't put alerts in the user API it takes forever to load them all
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'Alert', mappedBy: 'instigators')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $alerts;

    /**
     * @var Collection
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'UserRole', inversedBy: 'users')]
    #[ORM\JoinTable(name: 'user_x_user_role')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_role_id', referencedColumnName: 'user_role_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $roles;

    /**
     * @var Collection
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'Cohort', inversedBy: 'users')]
    #[ORM\JoinTable(name: 'user_x_cohort')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\InverseJoinColumn(name: 'cohort_id', referencedColumnName: 'cohort_id')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $cohorts;

    /**
     * @var CohortInterface
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\ManyToOne(targetEntity: 'Cohort')]
    #[ORM\JoinColumn(name: 'primary_cohort_id', referencedColumnName: 'cohort_id')]
    protected $primaryCohort;

    /**
     * @var ArrayCollection|PendingUserUpdateInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: 'PendingUserUpdate')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $pendingUserUpdates;

    /**
     * @var ArrayCollection|SchoolInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'School', mappedBy: 'directors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $directedSchools;

    /**
     * @var ArrayCollection|SchoolInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'School', mappedBy: 'administrators')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $administeredSchools;

    /**
     * @var ArrayCollection|ProgramInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'Program', mappedBy: 'directors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $directedPrograms;

    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type(type="boolean")
     * @IS\Expose
     * @IS\Type("boolean")
     */
    #[ORM\Column(name: 'root', type: 'boolean')]
    protected $root;

    /**
     * @var ArrayCollection|CurriculumInventoryReportInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'CurriculumInventoryReport', mappedBy: 'administrators')]
    #[ORM\OrderBy(['id' => 'ASC'])]
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

    /**
     * @return string
     */
    public function getLastName()
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

    /**
     * @return string
     */
    public function getFirstName()
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

    /**
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * @inheritdoc
     */
    public function getFirstAndLastName()
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    /**
     * @inheritdoc
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    /**
     * @inheritdoc
     */
    public function getDisplayName()
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

    /**
     * @return string
     */
    public function getPhone()
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

    /**
     * @return string
     */
    public function getEmail()
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

    /**
     * @return string
     */
    public function getPreferredEmail()
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

    /**
     * @return bool
     */
    public function isAddedViaIlios()
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

    /**
     * @return bool
     */
    public function isEnabled()
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

    /**
     * @return string
     */
    public function getCampusId()
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

    /**
     * @return string
     */
    public function getOtherId()
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

    /**
     * @return bool
     */
    public function isExamined()
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

    /**
     * @return bool
     */
    public function isUserSyncIgnore()
    {
        return $this->userSyncIgnore;
    }

    /**
     * @inheritdoc
     */
    public function generateIcsFeedKey()
    {
        $random = random_bytes(128);

        // and current time to give some more uniqueness
        $key = microtime() . '_' . $random;

        // hash the string to give consistent length and URL safe characters
        $this->icsFeedKey = hash('sha256', $key);
    }

    /**
     * @inheritdoc
     */
    public function setIcsFeedKey($icsFeedKey)
    {
        $this->icsFeedKey = $icsFeedKey;
    }

    /**
     * @inheritdoc
     */
    public function getIcsFeedKey()
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

    /**
     * @inheritdoc
     */
    public function getDirectedCourses()
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

    /**
     * @inheritdoc
     */
    public function getAdministeredCourses()
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

    /**
     * @inheritdoc
     */
    public function getStudentAdvisedCourses()
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

    /**
     * @inheritdoc
     */
    public function getAdministeredSessions()
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

    /**
     * @inheritdoc
     */
    public function getStudentAdvisedSessions()
    {
        return $this->studentAdvisedSessions;
    }

    /**
     * @inheritdoc
     */
    public function isDirectingCourse($courseId)
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

    /**
     * @return ArrayCollection|LearnerGroupInterface[]
     */
    public function getInstructedLearnerGroups()
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

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
    public function addLearnerIlmSession(IlmSessionInterface $session)
    {
        if (!$this->learnerIlmSessions->contains($session)) {
            $this->learnerIlmSessions->add($session);
            $session->addLearner($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeLearnerIlmSession(IlmSessionInterface $session)
    {
        if ($this->learnerIlmSessions->contains($session)) {
            $this->learnerIlmSessions->removeElement($session);
            $session->removeLearner($this);
        }
    }

    /**
     * @return ArrayCollection|IlmSessionInterface[]
     */
    public function getLearnerIlmSessions()
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

    /**
     * @return ArrayCollection|UserRoleInterface[]
     */
    public function getRoles()
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

    /**
     * @inheritdoc
     */
    public function getReports()
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

    /**
     * @return CohortInterface
     */
    public function getPrimaryCohort()
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

    /**
     * @inheritdoc
     */
    public function getInstructedOfferings()
    {
        return $this->instructedOfferings;
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthentication(AuthenticationInterface $authentication = null)
    {
        $this->authentication = $authentication;

        if ($authentication) {
            $authentication->setUser($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthentication()
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

    /**
     * @return ArrayCollection[AuditLogInterface]
     */
    public function getAuditLogs()
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

    /**
     * @return ArrayCollection|PendingUserUpdateInterface[]
     */
    public function getPendingUserUpdates()
    {
        return $this->pendingUserUpdates;
    }

    /**
     * @inheritdoc
     */
    public function addProgramYear(ProgramYearInterface $programYear)
    {
        if (!$this->programYears->contains($programYear)) {
            $this->programYears->add($programYear);
            $programYear->addDirector($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeProgramYear(ProgramYearInterface $programYear)
    {
        if ($this->programYears->contains($programYear)) {
            $this->programYears->removeElement($programYear);
            $programYear->removeDirector($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function addOffering(OfferingInterface $offering)
    {
        if (!$this->offerings->contains($offering)) {
            $this->offerings->add($offering);
            $offering->addLearner($this);
        }
    }

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
    public function getDirectedSchools()
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

    /**
     * @inheritdoc
     */
    public function getAdministeredSchools()
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

    /**
     * @inheritdoc
     */
    public function getDirectedPrograms()
    {
        return $this->directedPrograms;
    }

    /**
     * Get all the schools an user is affiliated with so we can match
     * permissions.
     *
     * @return ArrayCollection[School]
     */
    public function getAllSchools()
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

    /**
     * @inheritdoc
     */
    public function isRoot()
    {
        return $this->root;
    }

    /**
     * @inheritdoc
     */
    public function setRoot($root)
    {
        $this->root = $root;
    }

    /**
     * @inheritdoc
     */
    public function getAdministeredCurriculumInventoryReports()
    {
        return $this->administeredCurriculumInventoryReports;
    }

    /**
     * @inheritdoc
     */
    public function setAdministeredCurriculumInventoryReports(Collection $reports)
    {
        $this->administeredCurriculumInventoryReports = new ArrayCollection();

        foreach ($reports as $report) {
            $this->addAdministeredCurriculumInventoryReport($report);
        }
    }

    /**
     * @inheritdoc
     */
    public function addAdministeredCurriculumInventoryReport(CurriculumInventoryReportInterface $report)
    {
        if (!$this->administeredCurriculumInventoryReports->contains($report)) {
            $this->administeredCurriculumInventoryReports->add($report);
            $report->addAdministrator($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeAdministeredCurriculumInventoryReport(CurriculumInventoryReportInterface $report)
    {
        $this->administeredCurriculumInventoryReports->removeElement($report);
        $report->removeAdministrator($this);
    }
}
