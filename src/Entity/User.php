<?php

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

/**
 * Class User
 *
 * @ORM\Table(name="user", indexes={@ORM\Index(name="fkey_user_school", columns={"school_id"})})
 * @ORM\Entity(repositoryClass="App\Entity\Repository\UserRepository")
 *
 * @IS\Entity
 */
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
     *
     * @ORM\Column(name="user_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     *
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=30)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 30
     * )
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=20)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 20
     * )
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="middle_name", type="string", length=20, nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 20
     * )
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $middleName;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=30, nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 30
     * )
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100)
     *
     * @Assert\Email
     *
     * @Assert\NotBlank()
     *
     * @Assert\Length(
     *      min = 1,
     *      max = 100
     * )
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="preferred_email", type="string", length=100)
     *
     * @Assert\Email
     *
     * @Assert\Length(
     *      min = 1,
     *      max = 100
     * )
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $preferredEmail;

    /**
     * @var boolean
     *
     * @ORM\Column(name="added_via_ilios", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="boolean")
     *
     * @IS\Expose
     * @IS\Type("boolean")
     */
    protected $addedViaIlios;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="boolean")
     *
     * @IS\Expose
     * @IS\Type("boolean")
     */
    protected $enabled;

    /**
     * @var string
     *
     * @ORM\Column(name="uc_uid", type="string", length=16, nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 16
     * )
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $campusId;

    /**
     * @var string
     *
     * @ORM\Column(name="other_id", type="string", length=16, nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 16
     * )
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $otherId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="examined", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="boolean")
     *
     * @IS\Expose
     * @IS\Type("boolean")
     */
    protected $examined;

    /**
     * @var boolean
     *
     * @ORM\Column(name="user_sync_ignore", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="boolean")
     *
     * @IS\Expose
     * @IS\Type("boolean")
     */
    protected $userSyncIgnore;

    /**
     * @var string
     *
     * @ORM\Column(name="ics_feed_key", type="string", length=64, unique=true)
     *
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 64,
     *      max = 64
     * )
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $icsFeedKey;

    /**
     * @var AuthenticationInterface
     *
     * @ORM\OneToOne(targetEntity="Authentication", mappedBy="user")
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $authentication;

    /**
     * @var ArrayCollection|AuditLogInterface[]
     *
     * @ORM\OneToMany(targetEntity="AuditLog", mappedBy="user")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $auditLogs;

    /**
     * @var ArrayCollection|LearningMaterialInterface[]
     *
     * @ORM\OneToMany(targetEntity="LearningMaterial", mappedBy="owningUser")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * Don't put learningMaterials in the user API it takes forever to load them all
     * @IS\Type("entityCollection")
     */
    protected $learningMaterials;

    /**
     * @var ArrayCollection|ReportInterface[]
     *
     * @ORM\OneToMany(targetEntity="Report", mappedBy="user")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $reports;

    /**
     * @var SchoolInterface
     *
     * @ORM\ManyToOne(targetEntity="School")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="school_id", referencedColumnName="school_id", nullable=false)
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $school;

    /**
     * @var ArrayCollection|CourseInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Course", mappedBy="directors")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $directedCourses;

    /**
     * @var ArrayCollection|CourseInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Course", mappedBy="administrators")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $administeredCourses;

    /**
     * @var ArrayCollection|SessionInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Session", mappedBy="administrators")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $administeredSessions;

    /**
     * @var ArrayCollection|LearnerGroupInterface[]
     *
     * @ORM\ManyToMany(targetEntity="LearnerGroup", mappedBy="users")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $learnerGroups;

    /**
     * @var ArrayCollection|LearnerGroupInterface[]
     *
     * @ORM\ManyToMany(targetEntity="LearnerGroup", mappedBy="instructors")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $instructedLearnerGroups;

    /**
    * @var ArrayCollection|InstructorGroupInterface[]
    *
    * @ORM\ManyToMany(targetEntity="InstructorGroup", mappedBy="users")
     * @ORM\OrderBy({"id" = "ASC"})
    *
    * @IS\Expose
    * @IS\Type("entityCollection")
    */
    protected $instructorGroups;

    /**
     * @var ArrayCollection|IlmSessionInterface[]
     *
     * @ORM\ManyToMany(targetEntity="IlmSession", mappedBy="instructors")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $instructorIlmSessions;

    /**
     * @var ArrayCollection|IlmSessionInterface[]
     *
     * @ORM\ManyToMany(targetEntity="IlmSession", mappedBy="learners")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $learnerIlmSessions;

    /**
     * @var ArrayCollection|OfferingInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Offering", mappedBy="learners")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $offerings;

    /**
     * @var ArrayCollection|OfferingInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Offering", mappedBy="instructors")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $instructedOfferings;

    /**
     * @var ArrayCollection|ProgramYearInterface[]
     *
     * @ORM\ManyToMany(targetEntity="ProgramYear", mappedBy="directors")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $programYears;

    /**
     * @var ArrayCollection|AlertInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Alert", mappedBy="instigators")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * Don't put alerts in the user API it takes forever to load them all
     * @IS\Type("entityCollection")
     */
    protected $alerts;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="UserRole", inversedBy="users")
     * @ORM\JoinTable(name="user_x_user_role",
     *   joinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="user_role_id", referencedColumnName="user_role_id", onDelete="CASCADE")
     *   }
     * )
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $roles;

   /**
    * @var Collection
    *
    * @ORM\ManyToMany(targetEntity="Cohort", inversedBy="users")
    * @ORM\JoinTable(name="user_x_cohort",
    *   joinColumns={
    *     @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
    *   },
    *   inverseJoinColumns={
    *     @ORM\JoinColumn(name="cohort_id", referencedColumnName="cohort_id")
    *   }
    * )
    * @ORM\OrderBy({"id" = "ASC"})
    *
    * @IS\Expose
    * @IS\Type("entityCollection")
    */
    protected $cohorts;

    /**
     * @var CohortInterface
     *
     * @ORM\ManyToOne(targetEntity="Cohort")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="primary_cohort_id", referencedColumnName="cohort_id")
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $primaryCohort;

    /**
     * @var ArrayCollection|PendingUserUpdateInterface[]
     *
     * @ORM\OneToMany(targetEntity="PendingUserUpdate", mappedBy="user")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $pendingUserUpdates;

    /**
     * @var ArrayCollection|SchoolInterface[]
     *
     * @ORM\ManyToMany(targetEntity="School", mappedBy="directors")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $directedSchools;

    /**
     * @var ArrayCollection|SchoolInterface[]
     *
     * @ORM\ManyToMany(targetEntity="School", mappedBy="administrators")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $administeredSchools;

    /**
     * @var ArrayCollection|ProgramInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Program", mappedBy="directors")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $directedPrograms;

    /**
     * @var boolean
     *
     * @ORM\Column(name="root", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="boolean")
     *
     * @IS\Expose
     * @IS\Type("boolean")
     */
    protected $root;

    /**
     * @var ArrayCollection|CurriculumInventoryReportInterface[]
     *
     * @ORM\ManyToMany(targetEntity="CurriculumInventoryReport", mappedBy="administrators")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $administeredCurriculumInventoryReports;

    /**
     * Constructor
     */
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
     * @param boolean $addedViaIlios
     */
    public function setAddedViaIlios($addedViaIlios)
    {
        $this->addedViaIlios = $addedViaIlios;
    }

    /**
     * @return boolean
     */
    public function isAddedViaIlios()
    {
        return $this->addedViaIlios;
    }

    /**
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return boolean
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
     * @param boolean $examined
     */
    public function setExamined($examined)
    {
        $this->examined = $examined;
    }

    /**
     * @return boolean
     */
    public function isExamined()
    {
        return $this->examined;
    }

    /**
     * @param boolean $userSyncIgnore
     */
    public function setUserSyncIgnore($userSyncIgnore)
    {
        $this->userSyncIgnore = $userSyncIgnore;
    }

    /**
     * @return boolean
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

    /**
     * @param Collection $courses
     */
    public function setDirectedCourses(Collection $courses)
    {
        $this->directedCourses = new ArrayCollection();

        foreach ($courses as $course) {
            $this->addDirectedCourse($course);
        }
    }

    /**
     * @param CourseInterface $course
     */
    public function addDirectedCourse(CourseInterface $course)
    {
        if (!$this->directedCourses->contains($course)) {
            $this->directedCourses->add($course);
            $course->addDirector($this);
        }
    }

    /**
     * @param CourseInterface $course
     */
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

    /**
     * @param Collection $courses
     */
    public function setAdministeredCourses(Collection $courses)
    {
        $this->administeredCourses = new ArrayCollection();

        foreach ($courses as $course) {
            $this->addAdministeredCourse($course);
        }
    }

    /**
     * @param CourseInterface $course
     */
    public function addAdministeredCourse(CourseInterface $course)
    {
        if (!$this->administeredCourses->contains($course)) {
            $this->administeredCourses->add($course);
            $course->addAdministrator($this);
        }
    }

    /**
     * @param CourseInterface $course
     */
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

    /**
     * @param Collection $sessions
     */
    public function setAdministeredSessions(Collection $sessions)
    {
        $this->administeredSessions = new ArrayCollection();

        foreach ($sessions as $session) {
            $this->addAdministeredSession($session);
        }
    }

    /**
     * @param SessionInterface $session
     */
    public function addAdministeredSession(SessionInterface $session)
    {
        if (!$this->administeredSessions->contains($session)) {
            $this->administeredSessions->add($session);
            $session->addAdministrator($this);
        }
    }

    /**
     * @param SessionInterface $session
     */
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

    /**
     * @inheritdoc
     */
    public function isDirectingCourse($courseId)
    {
        return $this->directedCourses->map(function (CourseInterface $course) {
            return $course->getId();
        })->contains($courseId);
    }

    /**
     * @param LearnerGroupInterface $learnerGroup
     */
    public function addLearnerGroup(LearnerGroupInterface $learnerGroup)
    {
        if (!$this->learnerGroups->contains($learnerGroup)) {
            $this->learnerGroups->add($learnerGroup);
            $learnerGroup->addUser($this);
        }
    }

    /**
     * @param LearnerGroupInterface $learnerGroup
     */
    public function removeLearnerGroup(LearnerGroupInterface $learnerGroup)
    {
        if ($this->learnerGroups->contains($learnerGroup)) {
            $this->learnerGroups->removeElement($learnerGroup);
            $learnerGroup->removeUser($this);
        }
    }

    /**
     * @param Collection $instructedLearnerGroups
     */
    public function setInstructedLearnerGroups(Collection $instructedLearnerGroups)
    {
        $this->instructedLearnerGroups = new ArrayCollection();

        foreach ($instructedLearnerGroups as $instructedLearnerGroup) {
            $this->addInstructedLearnerGroup($instructedLearnerGroup);
        }
    }

    /**
     * @param LearnerGroupInterface $instructedLearnerGroup
     */
    public function addInstructedLearnerGroup(LearnerGroupInterface $instructedLearnerGroup)
    {
        if (!$this->instructedLearnerGroups->contains($instructedLearnerGroup)) {
            $this->instructedLearnerGroups->add($instructedLearnerGroup);
            $instructedLearnerGroup->addInstructor($this);
        }
    }

    /**
     * @param LearnerGroupInterface $instructedLearnerGroup
     */
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

    /**
     * @param InstructorGroupInterface $instructorGroup
     */
    public function addInstructorGroup(InstructorGroupInterface $instructorGroup)
    {
        if (!$this->instructorGroups->contains($instructorGroup)) {
            $this->instructorGroups->add($instructorGroup);
            $instructorGroup->addUser($this);
        }
    }

    /**
     * @param InstructorGroupInterface $instructorGroup
     */
    public function removeInstructorGroup(InstructorGroupInterface $instructorGroup)
    {
        $this->instructorGroups->removeElement($instructorGroup);
        $instructorGroup->removeUser($this);
    }

    /**
     * @param Collection $sessions
     */
    public function setInstructorIlmSessions(Collection $sessions)
    {
        $this->instructorIlmSessions = new ArrayCollection();

        foreach ($sessions as $session) {
            $this->addInstructorIlmSession($session);
        }
    }

    /**
     * @param IlmSessionInterface $session
     */
    public function addInstructorIlmSession(IlmSessionInterface $session)
    {
        if (!$this->instructorIlmSessions->contains($session)) {
            $this->instructorIlmSessions->add($session);
            $session->addInstructor($this);
        }
    }

    /**
     * @param IlmSessionInterface $session
     */
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

    /**
     * @param Collection $sessions
     */
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

    /**
     * @param AlertInterface $alert
     */
    public function addAlert(AlertInterface $alert)
    {
        if (!$this->alerts->contains($alert)) {
            $this->alerts->add($alert);
            $alert->addInstigator($this);
        }
    }

    /**
     * @param AlertInterface $alert
     */
    public function removeAlert(AlertInterface $alert)
    {
        $this->alerts->removeElement($alert);
        $alert->removeInstigator($this);
    }

    /**
     * @param Collection $roles
     */
    public function setRoles(Collection $roles)
    {
        $this->roles = new ArrayCollection();

        foreach ($roles as $role) {
            $this->addRole($role);
        }
    }

    /**
     * @param UserRoleInterface $role
     */
    public function addRole(UserRoleInterface $role)
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }
    }

    /**
     * @param UserRoleInterface $role
     */
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

    /**
     * @param Collection $reports
     */
    public function setReports(Collection $reports)
    {
        $this->reports = new ArrayCollection();

        foreach ($reports as $report) {
            $this->addReport($report);
        }
    }

    /**
     * @param ReportInterface $report
     */
    public function addReport(ReportInterface $report)
    {
        if (!$this->reports->contains($report)) {
            $this->reports->add($report);
        }
    }

    /**
     * @param ReportInterface $report
     */
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

    /**
     * @param CohortInterface $primaryCohort
     */
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

    /**
     * @param Collection $instructedOfferings
     */
    public function setInstructedOfferings(Collection $instructedOfferings)
    {
        $this->instructedOfferings = new ArrayCollection();

        foreach ($instructedOfferings as $instructedOffering) {
            $this->addInstructedOffering($instructedOffering);
        }
    }

    /**
     * @param Offering $instructedOffering
     */
    public function addInstructedOffering(Offering $instructedOffering)
    {
        if (!$this->instructedOfferings->contains($instructedOffering)) {
            $this->instructedOfferings->add($instructedOffering);
            $instructedOffering->addInstructor($this);
        }
    }

    /**
     * @param Offering $instructedOffering
     */
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

    /**
     * @param Collection $auditLogs
     */
    public function setAuditLogs(Collection $auditLogs)
    {
        $this->auditLogs = new ArrayCollection();

        foreach ($auditLogs as $auditLog) {
            $this->addAuditLog($auditLog);
        }
    }

    /**
     * @param AuditLogInterface $auditLog
     */
    public function addAuditLog(AuditLogInterface $auditLog)
    {
        if (!$this->auditLogs->contains($auditLog)) {
            $this->auditLogs->add($auditLog);
        }
    }

    /**
     * @param AuditLogInterface $auditLog
     */
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

    /**
     * @param Collection $pendingUserUpdates
     */
    public function setPendingUserUpdates(Collection $pendingUserUpdates)
    {
        $this->pendingUserUpdates = new ArrayCollection();

        foreach ($pendingUserUpdates as $pendingUserUpdate) {
            $this->addPendingUserUpdate($pendingUserUpdate);
        }
    }

    /**
     * @param PendingUserUpdateInterface $pendingUserUpdate
     */
    public function addPendingUserUpdate(PendingUserUpdateInterface $pendingUserUpdate)
    {
        if (!$this->pendingUserUpdates->contains($pendingUserUpdate)) {
            $this->pendingUserUpdates->add($pendingUserUpdate);
        }
    }

    /**
     * @param PendingUserUpdateInterface $pendingUserUpdate
     */
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

    /**
     * @param Collection $schools
     */
    public function setDirectedSchools(Collection $schools)
    {
        $this->directedSchools = new ArrayCollection();

        foreach ($schools as $school) {
            $this->addDirectedSchool($school);
        }
    }

    /**
     * @param SchoolInterface $school
     */
    public function addDirectedSchool(SchoolInterface $school)
    {
        if (!$this->directedSchools->contains($school)) {
            $this->directedSchools->add($school);
            $school->addDirector($this);
        }
    }

    /**
     * @param SchoolInterface $school
     */
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

    /**
     * @param Collection $schools
     */
    public function setAdministeredSchools(Collection $schools)
    {
        $this->administeredSchools = new ArrayCollection();

        foreach ($schools as $school) {
            $this->addAdministeredSchool($school);
        }
    }

    /**
     * @param SchoolInterface $school
     */
    public function addAdministeredSchool(SchoolInterface $school)
    {
        if (!$this->administeredSchools->contains($school)) {
            $this->administeredSchools->add($school);
            $school->addAdministrator($this);
        }
    }

    /**
     * @param SchoolInterface $school
     */
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

    /**
     * @param Collection $programs
     */
    public function setDirectedPrograms(Collection $programs)
    {
        $this->directedPrograms = new ArrayCollection();

        foreach ($programs as $program) {
            $this->addDirectedProgram($program);
        }
    }

    /**
     * @param ProgramInterface $program
     */
    public function addDirectedProgram(ProgramInterface $program)
    {
        if (!$this->directedPrograms->contains($program)) {
            $this->directedPrograms->add($program);
            $program->addDirector($this);
        }
    }

    /**
     * @param ProgramInterface $program
     */
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
        $cohortSchools = $this->getCohorts()->map(function (CohortInterface $cohort) {
            return $cohort->getSchool();
        });

        $directedCourseSchools = $this->getDirectedCourses()->map(function (CourseInterface $course) {
            return $course->getSchool();
        });

        $learnerGroupSchools = $this->getLearnerGroups()->map(function (LearnerGroupInterface $lg) {
            return $lg->getSchool();
        });

        $instructedLgSchools = $this->getInstructedLearnerGroups()->map(function (LearnerGroupInterface $lg) {
            return $lg->getSchool();
        });

        $instGroupSchools = $this->getInstructorGroups()->map(function (InstructorGroupInterface $ig) {
            return $ig->getSchool();
        });

        $insIlmSchools = $this->getInstructorIlmSessions()->map(function (IlmSessionInterface $ilm) {
            return $ilm->getSchool();
        });

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

        $schools = new ArrayCollection($allSchools);

        return $schools;
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
