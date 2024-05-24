<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\EnableableEntity;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\AlertableEntity;
use App\Traits\CohortsEntity;
use App\Traits\InstructorGroupsEntity;
use App\Traits\LearnerGroupsEntity;
use App\Traits\LearningMaterialsEntity;
use App\Attributes as IA;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;
use App\Traits\OfferingsEntity;
use App\Traits\ProgramYearsEntity;
use App\Traits\SchoolEntity;
use App\Repository\UserRepository;

#[ORM\Table(name: 'user')]
#[ORM\Index(columns: ["school_id"], name: "fkey_user_school")]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[IA\Entity]
class User implements UserInterface
{
    use EnableableEntity;
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

    #[ORM\Column(name: 'user_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected int $id;

    #[ORM\Column(name: 'last_name', type: 'string', length: 50)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 50)]
    protected string $lastName;

    #[ORM\Column(name: 'first_name', type: 'string', length: 50)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 50)]
    protected string $firstName;

    #[ORM\Column(name: 'middle_name', type: 'string', length: 20, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 20)]
    protected ?string $middleName = null;

    #[ORM\Column(name: 'display_name', type: 'string', length: 200, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 200)]
    protected ?string $displayName = null;

    #[ORM\Column(name: 'phone', type: 'string', length: 30, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 30)]
    protected ?string $phone = null;

    #[ORM\Column(name: 'email', type: 'string', length: 100)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Email]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 100)]
    protected string $email;

    #[ORM\Column(name: 'preferred_email', type: 'string', length: 100, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Email]
    #[Assert\Length(max: 100)]
    protected ?string $preferredEmail = null;

    #[ORM\Column(name: 'pronouns', type: 'string', length: 50, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Length(max: 50)]
    protected ?string $pronouns = null;

    #[ORM\Column(name: 'added_via_ilios', type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\NotNull]
    #[Assert\Type(type: 'boolean')]
    protected bool $addedViaIlios;

    #[ORM\Column(name: 'enabled', type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\NotNull]
    #[Assert\Type(type: 'boolean')]
    protected bool $enabled;

    #[ORM\Column(name: 'uc_uid', type: 'string', length: 16, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 16)]
    protected ?string $campusId = null;

    #[ORM\Column(name: 'other_id', type: 'string', length: 16, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 16)]
    protected ?string $otherId = null;

    #[ORM\Column(name: 'examined', type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\NotNull]
    #[Assert\Type(type: 'boolean')]
    protected bool $examined;

    #[ORM\Column(name: 'user_sync_ignore', type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\NotNull]
    #[Assert\Type(type: 'boolean')]
    protected bool $userSyncIgnore;

    #[ORM\Column(name: 'ics_feed_key', type: 'string', length: 64, unique: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 64, max: 64)]
    protected string $icsFeedKey;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: 'Authentication')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected ?AuthenticationInterface $authentication = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: 'AuditLog')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected Collection $auditLogs;

    /**
     * Don't put learningMaterials in the user API it takes forever to load them all
     */
    #[ORM\OneToMany(mappedBy: 'owningUser', targetEntity: 'LearningMaterial')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $learningMaterials;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: 'Report')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $reports;

    #[ORM\ManyToOne(targetEntity: 'School')]
    #[ORM\JoinColumn(name: 'school_id', referencedColumnName: 'school_id', nullable: false)]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected SchoolInterface $school;

    #[ORM\ManyToMany(targetEntity: 'Course', mappedBy: 'directors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $directedCourses;

    #[ORM\ManyToMany(targetEntity: 'Course', mappedBy: 'administrators')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $administeredCourses;

    #[ORM\ManyToMany(targetEntity: 'Course', mappedBy: 'studentAdvisors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $studentAdvisedCourses;

    #[ORM\ManyToMany(targetEntity: 'Session', mappedBy: 'administrators')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $administeredSessions;

    #[ORM\ManyToMany(targetEntity: 'Session', mappedBy: 'studentAdvisors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $studentAdvisedSessions;

    #[ORM\ManyToMany(targetEntity: 'LearnerGroup', mappedBy: 'users')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $learnerGroups;

    #[ORM\ManyToMany(targetEntity: 'LearnerGroup', mappedBy: 'instructors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $instructedLearnerGroups;

    #[ORM\ManyToMany(targetEntity: 'InstructorGroup', mappedBy: 'users')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $instructorGroups;

    #[ORM\ManyToMany(targetEntity: 'IlmSession', mappedBy: 'instructors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $instructorIlmSessions;

    #[ORM\ManyToMany(targetEntity: 'IlmSession', mappedBy: 'learners')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $learnerIlmSessions;

    #[ORM\ManyToMany(targetEntity: 'Offering', mappedBy: 'learners')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $offerings;

    #[ORM\ManyToMany(targetEntity: 'Offering', mappedBy: 'instructors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $instructedOfferings;

    #[ORM\ManyToMany(targetEntity: 'ProgramYear', mappedBy: 'directors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $programYears;

    /**
     * Don't put alerts in the user API it takes forever to load them all
     */
    #[ORM\ManyToMany(targetEntity: 'Alert', mappedBy: 'instigators')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $alerts;

    #[ORM\ManyToMany(targetEntity: 'UserRole', inversedBy: 'users')]
    #[ORM\JoinTable(name: 'user_x_user_role')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_role_id', referencedColumnName: 'user_role_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $roles;

    #[ORM\ManyToMany(targetEntity: 'Cohort', inversedBy: 'users')]
    #[ORM\JoinTable(name: 'user_x_cohort')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\InverseJoinColumn(name: 'cohort_id', referencedColumnName: 'cohort_id')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $cohorts;

    #[ORM\ManyToOne(targetEntity: 'Cohort')]
    #[ORM\JoinColumn(name: 'primary_cohort_id', referencedColumnName: 'cohort_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected ?CohortInterface $primaryCohort = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: 'PendingUserUpdate')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $pendingUserUpdates;

    #[ORM\ManyToMany(targetEntity: 'School', mappedBy: 'directors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $directedSchools;

    #[ORM\ManyToMany(targetEntity: 'School', mappedBy: 'administrators')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $administeredSchools;

    #[ORM\ManyToMany(targetEntity: 'Program', mappedBy: 'directors')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $directedPrograms;

    #[ORM\Column(name: 'root', type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\NotNull]
    #[Assert\Type(type: 'boolean')]
    protected bool $root;

    #[ORM\ManyToMany(targetEntity: 'CurriculumInventoryReport', mappedBy: 'administrators')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $administeredCurriculumInventoryReports;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserSessionMaterialStatus::class)]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $sessionMaterialStatuses;

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
        $this->sessionMaterialStatuses = new ArrayCollection();
        $this->addedViaIlios = false;
        $this->enabled = true;
        $this->examined = false;
        $this->userSyncIgnore = false;
        $this->root = false;

        $this->generateIcsFeedKey();
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setMiddleName(?string $middleName): void
    {
        $this->middleName = $middleName;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function getFirstAndLastName(): string
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    public function setDisplayName(?string $displayName): void
    {
        $this->displayName = $displayName;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setPreferredEmail(?string $email): void
    {
        $this->preferredEmail = $email;
    }

    public function getPreferredEmail(): ?string
    {
        return $this->preferredEmail;
    }

    public function setPronouns(?string $pronouns): void
    {
        $this->pronouns = $pronouns;
    }

    public function getPronouns(): ?string
    {
        return $this->pronouns;
    }

    public function setAddedViaIlios(bool $addedViaIlios): void
    {
        $this->addedViaIlios = $addedViaIlios;
    }

    public function isAddedViaIlios(): bool
    {
        return $this->addedViaIlios;
    }

    public function setCampusId(?string $campusId): void
    {
        $this->campusId = $campusId;
    }

    public function getCampusId(): ?string
    {
        return $this->campusId;
    }

    public function setOtherId(?string $otherId): void
    {
        $this->otherId = $otherId;
    }

    public function getOtherId(): ?string
    {
        return $this->otherId;
    }

    public function setExamined(bool $examined): void
    {
        $this->examined = $examined;
    }

    public function isExamined(): bool
    {
        return $this->examined;
    }

    public function setUserSyncIgnore(bool $userSyncIgnore): void
    {
        $this->userSyncIgnore = $userSyncIgnore;
    }

    public function isUserSyncIgnore(): bool
    {
        return $this->userSyncIgnore;
    }

    public function generateIcsFeedKey(): void
    {
        $random = random_bytes(128);

        // and current time to give some more uniqueness
        $key = microtime() . '_' . $random;

        // hash the string to give consistent length and URL safe characters
        $this->icsFeedKey = hash('sha256', $key);
    }

    public function setIcsFeedKey(string $icsFeedKey): void
    {
        $this->icsFeedKey = $icsFeedKey;
    }

    public function getIcsFeedKey(): string
    {
        return $this->icsFeedKey;
    }

    public function setDirectedCourses(Collection $courses): void
    {
        $this->directedCourses = new ArrayCollection();

        foreach ($courses as $course) {
            $this->addDirectedCourse($course);
        }
    }

    public function addDirectedCourse(CourseInterface $course): void
    {
        if (!$this->directedCourses->contains($course)) {
            $this->directedCourses->add($course);
            $course->addDirector($this);
        }
    }

    public function removeDirectedCourse(CourseInterface $course): void
    {
        $this->directedCourses->removeElement($course);
        $course->removeDirector($this);
    }

    public function getDirectedCourses(): Collection
    {
        return $this->directedCourses;
    }

    public function setAdministeredCourses(Collection $courses): void
    {
        $this->administeredCourses = new ArrayCollection();

        foreach ($courses as $course) {
            $this->addAdministeredCourse($course);
        }
    }

    public function addAdministeredCourse(CourseInterface $course): void
    {
        if (!$this->administeredCourses->contains($course)) {
            $this->administeredCourses->add($course);
            $course->addAdministrator($this);
        }
    }

    public function removeAdministeredCourse(CourseInterface $course): void
    {
        $this->administeredCourses->removeElement($course);
        $course->removeAdministrator($this);
    }

    public function getAdministeredCourses(): Collection
    {
        return $this->administeredCourses;
    }

    public function setStudentAdvisedCourses(Collection $courses): void
    {
        $this->studentAdvisedCourses = new ArrayCollection();

        foreach ($courses as $course) {
            $this->addStudentAdvisedCourse($course);
        }
    }

    public function addStudentAdvisedCourse(CourseInterface $course): void
    {
        if (!$this->studentAdvisedCourses->contains($course)) {
            $this->studentAdvisedCourses->add($course);
            $course->addStudentAdvisor($this);
        }
    }

    public function removeStudentAdvisedCourse(CourseInterface $course): void
    {
        $this->studentAdvisedCourses->removeElement($course);
        $course->removeStudentAdvisor($this);
    }

    public function getStudentAdvisedCourses(): Collection
    {
        return $this->studentAdvisedCourses;
    }

    public function setAdministeredSessions(Collection $sessions): void
    {
        $this->administeredSessions = new ArrayCollection();

        foreach ($sessions as $session) {
            $this->addAdministeredSession($session);
        }
    }

    public function addAdministeredSession(SessionInterface $session): void
    {
        if (!$this->administeredSessions->contains($session)) {
            $this->administeredSessions->add($session);
            $session->addAdministrator($this);
        }
    }

    public function removeAdministeredSession(SessionInterface $session): void
    {
        $this->administeredSessions->removeElement($session);
        $session->removeAdministrator($this);
    }

    public function getAdministeredSessions(): Collection
    {
        return $this->administeredSessions;
    }

    public function setStudentAdvisedSessions(Collection $sessions): void
    {
        $this->studentAdvisedSessions = new ArrayCollection();

        foreach ($sessions as $session) {
            $this->addStudentAdvisedSession($session);
        }
    }

    public function addStudentAdvisedSession(SessionInterface $session): void
    {
        if (!$this->studentAdvisedSessions->contains($session)) {
            $this->studentAdvisedSessions->add($session);
            $session->addStudentAdvisor($this);
        }
    }

    public function removeStudentAdvisedSession(SessionInterface $session): void
    {
        $this->studentAdvisedSessions->removeElement($session);
        $session->removeStudentAdvisor($this);
    }

    public function getStudentAdvisedSessions(): Collection
    {
        return $this->studentAdvisedSessions;
    }

    public function isDirectingCourse(int $courseId): bool
    {
        return $this->directedCourses->map(fn(CourseInterface $course) => $course->getId())->contains($courseId);
    }

    public function addLearnerGroup(LearnerGroupInterface $learnerGroup): void
    {
        if (!$this->learnerGroups->contains($learnerGroup)) {
            $this->learnerGroups->add($learnerGroup);
            $learnerGroup->addUser($this);
        }
    }

    public function removeLearnerGroup(LearnerGroupInterface $learnerGroup): void
    {
        if ($this->learnerGroups->contains($learnerGroup)) {
            $this->learnerGroups->removeElement($learnerGroup);
            $learnerGroup->removeUser($this);
        }
    }

    public function setInstructedLearnerGroups(Collection $learnerGroups): void
    {
        $this->instructedLearnerGroups = new ArrayCollection();

        foreach ($learnerGroups as $learnerGroup) {
            $this->addInstructedLearnerGroup($learnerGroup);
        }
    }

    public function addInstructedLearnerGroup(LearnerGroupInterface $learnerGroup): void
    {
        if (!$this->instructedLearnerGroups->contains($learnerGroup)) {
            $this->instructedLearnerGroups->add($learnerGroup);
            $learnerGroup->addInstructor($this);
        }
    }

    public function removeInstructedLearnerGroup(LearnerGroupInterface $learnerGroup): void
    {
        $this->instructedLearnerGroups->removeElement($learnerGroup);
        $learnerGroup->removeInstructor($this);
    }

    public function getInstructedLearnerGroups(): Collection
    {
        return $this->instructedLearnerGroups;
    }

    public function addInstructorGroup(InstructorGroupInterface $instructorGroup): void
    {
        if (!$this->instructorGroups->contains($instructorGroup)) {
            $this->instructorGroups->add($instructorGroup);
            $instructorGroup->addUser($this);
        }
    }

    public function removeInstructorGroup(InstructorGroupInterface $instructorGroup): void
    {
        $this->instructorGroups->removeElement($instructorGroup);
        $instructorGroup->removeUser($this);
    }

    public function setInstructorIlmSessions(Collection $sessions): void
    {
        $this->instructorIlmSessions = new ArrayCollection();

        foreach ($sessions as $session) {
            $this->addInstructorIlmSession($session);
        }
    }

    public function addInstructorIlmSession(IlmSessionInterface $session): void
    {
        if (!$this->instructorIlmSessions->contains($session)) {
            $this->instructorIlmSessions->add($session);
            $session->addInstructor($this);
        }
    }

    public function removeInstructorIlmSession(IlmSessionInterface $session): void
    {
        $this->instructorIlmSessions->removeElement($session);
        $session->removeInstructor($this);
    }

    public function getInstructorIlmSessions(): Collection
    {
        return $this->instructorIlmSessions;
    }

    public function setLearnerIlmSessions(Collection $ilmSessions): void
    {
        $this->learnerIlmSessions = new ArrayCollection();

        foreach ($ilmSessions as $ilmSession) {
            $this->addLearnerIlmSession($ilmSession);
        }
    }

    public function addLearnerIlmSession(IlmSessionInterface $ilmSession): void
    {
        if (!$this->learnerIlmSessions->contains($ilmSession)) {
            $this->learnerIlmSessions->add($ilmSession);
            $ilmSession->addLearner($this);
        }
    }

    public function removeLearnerIlmSession(IlmSessionInterface $ilmSession): void
    {
        if ($this->learnerIlmSessions->contains($ilmSession)) {
            $this->learnerIlmSessions->removeElement($ilmSession);
            $ilmSession->removeLearner($this);
        }
    }

    public function getLearnerIlmSessions(): Collection
    {
        return $this->learnerIlmSessions;
    }

    public function addAlert(AlertInterface $alert): void
    {
        if (!$this->alerts->contains($alert)) {
            $this->alerts->add($alert);
            $alert->addInstigator($this);
        }
    }

    public function removeAlert(AlertInterface $alert): void
    {
        $this->alerts->removeElement($alert);
        $alert->removeInstigator($this);
    }

    public function setRoles(Collection $roles): void
    {
        $this->roles = new ArrayCollection();

        foreach ($roles as $role) {
            $this->addRole($role);
        }
    }

    public function addRole(UserRoleInterface $role): void
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }
    }

    public function removeRole(UserRoleInterface $role): void
    {
        $this->roles->removeElement($role);
    }

    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function setReports(Collection $reports): void
    {
        $this->reports = new ArrayCollection();

        foreach ($reports as $report) {
            $this->addReport($report);
        }
    }

    public function addReport(ReportInterface $report): void
    {
        if (!$this->reports->contains($report)) {
            $this->reports->add($report);
        }
    }

    public function removeReport(ReportInterface $report): void
    {
        $this->reports->removeElement($report);
    }

    public function getReports(): Collection
    {
        return $this->reports;
    }

    /**
     * Remove Primary cohort if it is no longer a cohorts
     */
    public function setCohorts(Collection $cohorts): void
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
     */
    public function removeCohort(CohortInterface $cohort): void
    {
        $this->cohorts->removeElement($cohort);
        $primaryCohort = $this->getPrimaryCohort();
        if ($primaryCohort && $cohort === $primaryCohort) {
            $this->setPrimaryCohort(null);
        }
    }

    public function setPrimaryCohort(?CohortInterface $primaryCohort = null): void
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

    public function setInstructedOfferings(Collection $instructedOfferings): void
    {
        $this->instructedOfferings = new ArrayCollection();

        foreach ($instructedOfferings as $instructedOffering) {
            $this->addInstructedOffering($instructedOffering);
        }
    }

    public function addInstructedOffering(Offering $instructedOffering): void
    {
        if (!$this->instructedOfferings->contains($instructedOffering)) {
            $this->instructedOfferings->add($instructedOffering);
            $instructedOffering->addInstructor($this);
        }
    }

    public function removeInstructedOffering(Offering $instructedOffering): void
    {
        $this->instructedOfferings->removeElement($instructedOffering);
        $instructedOffering->removeInstructor($this);
    }

    public function getInstructedOfferings(): Collection
    {
        return $this->instructedOfferings;
    }

    public function setAuthentication(?AuthenticationInterface $authentication = null): void
    {
        $this->authentication = $authentication;

        $authentication?->setUser($this);
    }

    public function getAuthentication(): ?AuthenticationInterface
    {
        return $this->authentication;
    }

    public function setAuditLogs(Collection $auditLogs): void
    {
        $this->auditLogs = new ArrayCollection();

        foreach ($auditLogs as $auditLog) {
            $this->addAuditLog($auditLog);
        }
    }

    public function addAuditLog(AuditLogInterface $auditLog): void
    {
        if (!$this->auditLogs->contains($auditLog)) {
            $this->auditLogs->add($auditLog);
        }
    }

    public function removeAuditLog(AuditLogInterface $auditLog): void
    {
        $this->auditLogs->removeElement($auditLog);
    }

    public function getAuditLogs(): Collection
    {
        return $this->auditLogs;
    }

    public function setPendingUserUpdates(Collection $pendingUserUpdates): void
    {
        $this->pendingUserUpdates = new ArrayCollection();

        foreach ($pendingUserUpdates as $pendingUserUpdate) {
            $this->addPendingUserUpdate($pendingUserUpdate);
        }
    }

    public function addPendingUserUpdate(PendingUserUpdateInterface $pendingUserUpdate): void
    {
        if (!$this->pendingUserUpdates->contains($pendingUserUpdate)) {
            $this->pendingUserUpdates->add($pendingUserUpdate);
        }
    }

    public function removePendingUserUpdate(PendingUserUpdateInterface $pendingUserUpdate): void
    {
        $this->pendingUserUpdates->removeElement($pendingUserUpdate);
    }

    public function getPendingUserUpdates(): Collection
    {
        return $this->pendingUserUpdates;
    }

    public function addProgramYear(ProgramYearInterface $programYear): void
    {
        if (!$this->programYears->contains($programYear)) {
            $this->programYears->add($programYear);
            $programYear->addDirector($this);
        }
    }

    public function removeProgramYear(ProgramYearInterface $programYear): void
    {
        if ($this->programYears->contains($programYear)) {
            $this->programYears->removeElement($programYear);
            $programYear->removeDirector($this);
        }
    }

    public function addOffering(OfferingInterface $offering): void
    {
        if (!$this->offerings->contains($offering)) {
            $this->offerings->add($offering);
            $offering->addLearner($this);
        }
    }

    public function removeOffering(OfferingInterface $offering): void
    {
        if ($this->offerings->contains($offering)) {
            $this->offerings->removeElement($offering);
            $offering->removeLearner($this);
        }
    }

    public function setDirectedSchools(Collection $schools): void
    {
        $this->directedSchools = new ArrayCollection();

        foreach ($schools as $school) {
            $this->addDirectedSchool($school);
        }
    }

    public function addDirectedSchool(SchoolInterface $school): void
    {
        if (!$this->directedSchools->contains($school)) {
            $this->directedSchools->add($school);
            $school->addDirector($this);
        }
    }

    public function removeDirectedSchool(SchoolInterface $school): void
    {
        $this->directedSchools->removeElement($school);
        $school->removeDirector($this);
    }

    public function getDirectedSchools(): Collection
    {
        return $this->directedSchools;
    }

    public function setAdministeredSchools(Collection $schools): void
    {
        $this->administeredSchools = new ArrayCollection();

        foreach ($schools as $school) {
            $this->addAdministeredSchool($school);
        }
    }

    public function addAdministeredSchool(SchoolInterface $school): void
    {
        if (!$this->administeredSchools->contains($school)) {
            $this->administeredSchools->add($school);
            $school->addAdministrator($this);
        }
    }

    public function removeAdministeredSchool(SchoolInterface $school): void
    {
        $this->administeredSchools->removeElement($school);
        $school->removeAdministrator($this);
    }

    public function getAdministeredSchools(): Collection
    {
        return $this->administeredSchools;
    }

    public function setDirectedPrograms(Collection $programs): void
    {
        $this->directedPrograms = new ArrayCollection();

        foreach ($programs as $program) {
            $this->addDirectedProgram($program);
        }
    }

    public function addDirectedProgram(ProgramInterface $program): void
    {
        if (!$this->directedPrograms->contains($program)) {
            $this->directedPrograms->add($program);
            $program->addDirector($this);
        }
    }

    public function removeDirectedProgram(ProgramInterface $program): void
    {
        $this->directedPrograms->removeElement($program);
        $program->removeDirector($this);
    }

    public function getDirectedPrograms(): Collection
    {
        return $this->directedPrograms;
    }

    /**
     * Get all the schools a user is affiliated with, so we can match
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

    public function setRoot(bool $root): void
    {
        $this->root = $root;
    }

    public function getAdministeredCurriculumInventoryReports(): Collection
    {
        return $this->administeredCurriculumInventoryReports;
    }

    public function setAdministeredCurriculumInventoryReports(Collection $reports): void
    {
        $this->administeredCurriculumInventoryReports = new ArrayCollection();

        foreach ($reports as $report) {
            $this->addAdministeredCurriculumInventoryReport($report);
        }
    }

    public function addAdministeredCurriculumInventoryReport(CurriculumInventoryReportInterface $report): void
    {
        if (!$this->administeredCurriculumInventoryReports->contains($report)) {
            $this->administeredCurriculumInventoryReports->add($report);
            $report->addAdministrator($this);
        }
    }

    public function removeAdministeredCurriculumInventoryReport(CurriculumInventoryReportInterface $report): void
    {
        $this->administeredCurriculumInventoryReports->removeElement($report);
        $report->removeAdministrator($this);
    }

    public function setSessionMaterialStatuses(Collection $sessionMaterialStatuses): void
    {
        $this->sessionMaterialStatuses = new ArrayCollection();

        foreach ($sessionMaterialStatuses as $slm) {
            $this->addSessionMaterialStatus($slm);
        }
    }

    public function addSessionMaterialStatus(UserSessionMaterialStatus $sessionMaterialStatus): void
    {
        if (!$this->sessionMaterialStatuses->contains($sessionMaterialStatus)) {
            $this->sessionMaterialStatuses->add($sessionMaterialStatus);
        }
    }

    public function removeSessionMaterialStatus(UserSessionMaterialStatus $sessionMaterialStatus): void
    {
        $this->sessionMaterialStatuses->removeElement($sessionMaterialStatus);
    }

    public function getSessionMaterialStatuses(): Collection
    {
        return $this->sessionMaterialStatuses;
    }
}
