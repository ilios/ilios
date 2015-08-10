<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Encoder\EncoderAwareInterface;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;
use Ilios\CoreBundle\Traits\OfferingsEntity;
use Ilios\CoreBundle\Traits\ProgramYearsEntity;

/**
 * Class User
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="user", indexes={@ORM\Index(name="fkey_user_primary_school", columns={"primary_school_id"})})
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\UserRepository")
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessType("public_method")
 */
class User implements UserInterface, EncoderAwareInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;
    use OfferingsEntity;
    use ProgramYearsEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
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
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("lastName")
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
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("firstName")
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
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("middleName")
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
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100)
     *
     * @Assert\Email(checkMX = false)
     *
     * @Assert\NotBlank()
     *
     * @Assert\Length(
     *      min = 1,
     *      max = 100
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $email;

    /**
     * @var boolean
     *
     * @ORM\Column(name="added_via_ilios", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="boolean")
     *
     * @JMS\SerializedName("addedViaIlios")
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
     * @JMS\Expose
     * @JMS\Type("boolean")
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
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("ucUid")
     */
    protected $ucUid;

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
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("otherId")
     */
    protected $otherId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="examined", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="boolean")
     */
    protected $examined;

    /**
     * @var boolean
     *
     * @ORM\Column(name="user_sync_ignore", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="boolean")
     */
    protected $userSyncIgnore;

    /**
     * @var ApiKeyInterface
     *
     * @ORM\OneToOne(targetEntity="ApiKey", mappedBy="user")
     */
    protected $apiKey;

    /**
     * @var AuthenticationInterface
     *
     * @ORM\OneToOne(targetEntity="Authentication", mappedBy="user")
     */
    protected $authentication;

    /**
     * @var ArrayCollection|UserMadeReminderInterface[]
     *
     * @ORM\OneToMany(targetEntity="UserMadeReminder", mappedBy="user")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $reminders;

    /**
     * @var ArrayCollection|LearningMaterialInterface[]
     *
     * @ORM\OneToMany(targetEntity="LearningMaterial", mappedBy="owningUser")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("learningMaterials")
     */
    protected $learningMaterials;

    /**
     * @var ArrayCollection|PublishEventInterface[]
     *
     * @ORM\OneToMany(targetEntity="PublishEvent", mappedBy="administrator")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("publishEvents")
     */
    protected $publishEvents;

    /**
     * @var ArrayCollection|ReportInterface[]
     *
     * @ORM\OneToMany(targetEntity="Report", mappedBy="user")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $reports;

    /**
     * @var SchoolInterface
     *
     * @ORM\ManyToOne(targetEntity="School")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="primary_school_id", referencedColumnName="school_id")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("primarySchool")
     */
    protected $primarySchool;

    /**
     * @var ArrayCollection|CourseInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Course", mappedBy="directors")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("directedCourses")
     */
    protected $directedCourses;

    /**
     * @var ArrayCollection|LearnerGroupInterface[]
     *
     * @ORM\ManyToMany(targetEntity="LearnerGroup", mappedBy="users")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("learnerGroups")
     */
    protected $learnerGroups;

    /**
     * @var ArrayCollection|LearnerGroupInterface[]
     *
     * @ORM\ManyToMany(targetEntity="LearnerGroup", mappedBy="instructorUsers")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("instructorUserGroups")
     */
    protected $instructorUserGroups;

    /**
    * @var ArrayCollection|InstructorGroupInterface[]
    *
    * @ORM\ManyToMany(targetEntity="InstructorGroup", mappedBy="users")
    *
    * @JMS\Expose
    * @JMS\Type("array<string>")
    * @JMS\SerializedName("instructorGroups")
    */
    protected $instructorGroups;

    /**
    * @var ArrayCollection|IlmSessionInterface[]
    *
    * @ORM\ManyToMany(targetEntity="IlmSession", mappedBy="instructors")
    *
    * @JMS\Expose
    * @JMS\Type("array<string>")
    * @JMS\SerializedName("instructorIlmSessions")
    */
    protected $instructorIlmSessions;

    /**
    * @var ArrayCollection|IlmSessionInterface[]
    *
    * @ORM\ManyToMany(targetEntity="IlmSession", mappedBy="learners")
    *
    * @JMS\Expose
    * @JMS\Type("array<string>")
    * @JMS\SerializedName("learnerIlmSessions")
    */
    protected $learnerIlmSessions;

    /**
     * @var ArrayCollection|OfferingInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Offering", mappedBy="learners")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $offerings;

    /**
     * @var ArrayCollection|OfferingInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Offering", mappedBy="instructors")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("instructedOfferings")
     */
    protected $instructedOfferings;

    /**
    * @var ArrayCollection|ProgramYearInterface[]
    *
    * @ORM\ManyToMany(targetEntity="ProgramYear", mappedBy="directors")
    *
    * @JMS\Expose
    * @JMS\Type("array<string>")
    * @JMS\SerializedName("programYears")
    */
    protected $programYears;

    /**
    * @var ArrayCollection|InstructionHoursInterface[]
    *
    * @ORM\OneToMany(targetEntity="InstructionHours", mappedBy="user")
    *
    * @JMS\Expose
    * @JMS\Type("array<string>")
    * @JMS\SerializedName("instructionHours")
    */
    protected $instructionHours;

    /**
     * @var ArrayCollection|AlertInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Alert", mappedBy="instigators")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
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
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
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
    *
    * @JMS\Expose
    * @JMS\Type("array<string>")
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
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("primaryCohort")
     */
    protected $primaryCohort;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->reminders            = new ArrayCollection();
        $this->directedCourses      = new ArrayCollection();
        $this->learnerGroups        = new ArrayCollection();
        $this->instructorUserGroups = new ArrayCollection();
        $this->instructorGroups     = new ArrayCollection();
        $this->offerings            = new ArrayCollection();
        $this->instructedOfferings  = new ArrayCollection();
        $this->programYears         = new ArrayCollection();
        $this->alerts               = new ArrayCollection();
        $this->roles                = new ArrayCollection();
        $this->learningMaterials    = new ArrayCollection();
        $this->publishEvents        = new ArrayCollection();
        $this->reports              = new ArrayCollection();
        $this->instructionHours     = new ArrayCollection();
        $this->cohorts              = new ArrayCollection();
        $this->addedViaIlios = false;
        $this->enabled = true;
        $this->examined = false;
        $this->userSyncIgnore = false;
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
     * @param string $ucUid
     */
    public function setUcUid($ucUid)
    {
        $this->ucUid = $ucUid;
    }

    /**
     * @return string
     */
    public function getUcUid()
    {
        return $this->ucUid;
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
     * @param ApiKeyInterface $apiKey
     */
    public function setApiKey(ApiKeyInterface $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return ApiKeyInterface
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param Collection $reminders
     */
    public function setReminders(Collection $reminders)
    {
        $this->reminders = new ArrayCollection();

        foreach ($reminders as $reminder) {
            $this->addReminder($reminder);
        }
    }

    /**
     * @param UserMadeReminderInterface $reminder
     */
    public function addReminder(UserMadeReminderInterface $reminder)
    {
        $this->reminders->add($reminder);
    }

    /**
     * @return ArrayCollection|UserMadeReminderInterface[]
     */
    public function getReminders()
    {
        return $this->reminders;
    }

    /**
     * @param SchoolInterface $primarySchool
     */
    public function setPrimarySchool(SchoolInterface $primarySchool)
    {
        $this->primarySchool = $primarySchool;
    }

    /**
     * @return SchoolInterface
     */
    public function getPrimarySchool()
    {
        return $this->primarySchool;
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
        $this->directedCourses->add($course);
    }

    /**
     * @return ArrayCollection|CourseInterface[]
     */
    public function getDirectedCourses()
    {
        //criteria not 100% reliale on many to many relationships
        //fix in https://github.com/doctrine/doctrine2/pull/1399
        // $criteria = Criteria::create()->where(Criteria::expr()->eq("deleted", false));
        // return new ArrayCollection($this->directedCourses->matching($criteria)->getValues());
        
        $arr = $this->directedCourses->filter(function ($entity) {
            return !$entity->isDeleted();
        })->toArray();
        
        $reIndexed = array_values($arr);
        
        return new ArrayCollection($reIndexed);
    }

    /**
     * @param Collection $learnerGroups
     */
    public function setLearnerGroups(Collection $learnerGroups)
    {
        $this->userGroups = new ArrayCollection();

        foreach ($learnerGroups as $group) {
            $this->addLearnerGroup($group);
        }
    }

    /**
     * @param LearnerGroupInterface $userGroup
     */
    public function addLearnerGroup(LearnerGroupInterface $learnerGroup)
    {
        $this->learnerGroups->add($learnerGroup);
    }

    /**
     * @return ArrayCollection|LearnerGroupInterface[]
     */
    public function getLearnerGroups()
    {
        return $this->learnerGroups;
    }

    /**
     * @param Collection $instructorUserGroups
     */
    public function setInstructorUserGroups(Collection $instructorUserGroups)
    {
        $this->instructorUserGroups = new ArrayCollection();

        foreach ($instructorUserGroups as $instructorUserGroup) {
            $this->addInstructorUserGroup($instructorUserGroup);
        }
    }

    /**
     * @param LearnerGroupInterface $instructorUserGroup
     */
    public function addInstructorUserGroup(LearnerGroupInterface $instructorUserGroup)
    {
        $this->instructorUserGroups->add($instructorUserGroup);
    }

    /**
     * @return ArrayCollection|LearnerGroupInterface[]
     */
    public function getInstructorUserGroups()
    {
        return $this->instructorUserGroups;
    }

    /**
     * @param Collection $instructorGroups
     */
    public function setInstructorGroups(Collection $instructorGroups)
    {
        $this->instructorGroups = new ArrayCollection();

        foreach ($instructorGroups as $instructorGroup) {
            $this->addInstructorGroup($instructorGroup);
        }
    }

    /**
     * @param InstructorGroupInterface $instructorGroup
     */
    public function addInstructorGroup(InstructorGroupInterface $instructorGroup)
    {
        $this->instructorGroups->add($instructorGroup);
    }

    /**
     * @return ArrayCollection|InstructorGroupInterface[]
     */
    public function getInstructorGroups()
    {
        return $this->instructorGroups;
    }

    /**
     * @param Collection $sessions
     */
    public function setInstructorIlmSessions(Collection $sessions)
    {
        $this->instructorIlmSessions = new ArrayCollection();

        foreach ($sessions as $session) {
            $this->addInstructorIlmSessions($session);
        }
    }

    /**
     * @param IlmSessionInterface $session
     */
    public function addInstructorIlmSessions(IlmSessionInterface $session)
    {
        $this->instructorIlmSessions->add($session);
    }

    /**
     * @return ArrayCollection|IlmSessionInterface[]
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
            $this->addLearnerIlmSessions($session);
        }
    }

    /**
     * @param IlmSessionInterface $session
     */
    public function addLearnerIlmSessions(IlmSessionInterface $session)
    {
        $this->learnerIlmSessions->add($session);
    }

    /**
     * @return ArrayCollection|IlmSessionInterface[]
     */
    public function getLearnerIlmSessions()
    {
        return $this->learnerIlmSessions;
    }

    /**
     * @param Collection $alerts
     */
    public function setAlerts(Collection $alerts)
    {
        $this->alerts = new ArrayCollection();

        foreach ($alerts as $alert) {
            $this->addAlert($alert);
        }
    }

    /**
     * @param AlertInterface $alert
     */
    public function addAlert(AlertInterface $alert)
    {
        $this->alerts->add($alert);
    }

    /**
     * @return ArrayCollection|AlertInterface[]
     */
    public function getAlerts()
    {
        return $this->alerts;
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
        $this->roles->add($role);
    }

    /**
     * @return ArrayCollection|UserRoleInterface[]
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param Collection $learningMaterials
     */
    public function setLearningMaterials(Collection $learningMaterials)
    {
        $this->learningMaterials = new ArrayCollection();

        foreach ($learningMaterials as $learningMaterial) {
            $this->addLearningMaterial($learningMaterial);
        }
    }

    /**
     * @param LearningMaterialInterface $learningMaterial
     */
    public function addLearningMaterial(LearningMaterialInterface $learningMaterial)
    {
        $this->learningMaterials->add($learningMaterial);
    }

    /**
     * @return ArrayCollection|LearningMaterialInterface[]
     */
    public function getLearningMaterials()
    {
        return $this->learningMaterials;
    }

    /**
     * @param Collection $publishEvents
     */
    public function setPublishEvents(Collection $publishEvents)
    {
        $this->publishEvents = new ArrayCollection();

        foreach ($publishEvents as $publishEvent) {
            $this->addPublishEvent($publishEvent);
        }
    }

    /**
     * @param PublishEventInterface $publishEvent
     */
    public function addPublishEvent(PublishEventInterface $publishEvent)
    {
        $this->publishEvents->add($publishEvent);
    }

    /**
     * @return ArrayCollection|PublishEventInterface[]
     */
    public function getPublishEvents()
    {
        return $this->publishEvents;
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
        $this->reports->add($report);
    }

    /**
     * @return ArrayCollection|ReportInterface[]
     */
    public function getReports()
    {
        //criteria not 100% reliale on many to many relationships
        //fix in https://github.com/doctrine/doctrine2/pull/1399
        // $criteria = Criteria::create()->where(Criteria::expr()->eq("deleted", false));
        // return new ArrayCollection($this->reports->matching($criteria)->getValues());
        
        $arr = $this->reports->filter(function ($entity) {
            return !$entity->isDeleted();
        })->toArray();
        
        $reIndexed = array_values($arr);
        
        return new ArrayCollection($reIndexed);
    }

    /**
     * @param Collection $instructionHours
     */
    public function setInstructionHours(Collection $instructionHours)
    {
        $this->instructionHours = new ArrayCollection();

        foreach ($instructionHours as $instructionHour) {
            $this->addInstructionHours($instructionHour);
        }
    }

    /**
     * @param InstructionHoursInterface $report
     */
    public function addInstructionHours(InstructionHoursInterface $instructionHours)
    {
        $this->instructionHours->add($instructionHours);
    }

    /**
     * @return ArrayCollection|InstructionHoursInterface[]
     */
    public function getInstructionHours()
    {
        return $this->instructionHours;
    }

    /**
    * @param Collection $cohorts
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
    * @param CohortInterface $report
    */
    public function addCohort(CohortInterface $cohort)
    {
        $this->cohorts->add($cohort);
    }

    /**
    * @return CohortInterface[]|ArrayCollection
    */
    public function getCohorts()
    {
        return $this->cohorts;
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
     * @param Collection $instructedOffering
     */
    public function setInstructedOfferings(Collection $instructedOfferings)
    {
        $this->instructedOffering = new ArrayCollection();

        foreach ($instructedOfferings as $instructedOffering) {
            $this->addInstructedOffering($instructedOffering);
        }
    }

    /**
     * @param Offering $report
     */
    public function addInstructedOffering(Offering $instructedOffering)
    {
        $this->instructedOfferings->add($instructedOffering);
    }

    /**
     * @return ArrayCollection|Offering[]
     */
    public function getInstructedOfferings()
    {
        return $this->instructedOfferings;
    }

    /**
     * @param AuthenticationInterface $authentication
     */
    public function setAuthentication(CohortInterface $authentication = null)
    {
        $this->authentication = $authentication;
    }

    /**
     * @return AuthenticationInterface
     */
    public function getAuthentication()
    {
        return $this->authentication;
    }

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        return serialize(array(
                $this->userId,
                $this->ucUid,
                $this->email
            ));

    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        list (
            $this->userId,
            $this->ucUid,
            $this->email
            ) = unserialize($serialized);
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {

    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        $newPassword = $this->getAuthentication()->getPasswordBcrypt();
        $legacyPassword = $this->getAuthentication()->getPasswordSha256();

        return $newPassword?$newPassword:$legacyPassword;
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return (string) $this->id;
    }

    /**
     * Use the old ilios legacy encoder for accounts
     * that haven't changed their password
     * @return [type] [description]
     */
    public function getEncoderName()
    {
        if ($this->getAuthentication()->isLegacyAccount()) {
            return 'ilios_legacy_encoder';
        }

        return null; // use the default encoder
    }
}
