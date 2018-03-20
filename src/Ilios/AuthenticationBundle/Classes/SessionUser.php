<?php

namespace Ilios\AuthenticationBundle\Classes;

use Ilios\CoreBundle\Entity\Manager\UserManager;
use Ilios\CoreBundle\Entity\SchoolInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Ilios\CoreBundle\Entity\UserInterface as IliosUserInterface;
use DateTime;

/**
 * Class SessionUser
 *
 * A session user is a static serializable representation
 * of a single user.  It is used in our authentication system
 * to avoid issues with a user being able to update their own data.
 */
class SessionUser implements SessionUserInterface
{
    /**
     * @var array
     */
    protected $nonStudentSchoolIds;

    /**
     * @var integer
     */
    protected $userId;

    /**
     * @var bool
     */
    protected $isRoot;

    /**
     * @var bool
     */
    protected $isEnabled;

    /**
     * @var DateTime
     */
    protected $tokenNotValidBefore;

    /**
     * @var integer
     */
    protected $schoolId;

    /**
     * @var bool
     */
    protected $isLegacyAccount;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var array
     */
    protected $directedCourseIds;

    /**
     * @var array
     */
    protected $administeredCourseIds;

    /**
     * @var array
     */
    protected $directedSchoolIds;

    /**
     * @var array
     */
    protected $administeredSchoolIds;

    /**
     * @var array
     */
    protected $directedCourseSchoolIds;

    /**
     * @var array
     */
    protected $administeredCourseSchoolIds;

    /**
     * @var array
     */
    protected $administeredSessionSchoolIds;

    /**
     * @var array
     */
    protected $administeredSessionCourseIds;

    /**
     * @var array
     */
    protected $taughtCourseIds;

    /**
     * @var array
     */
    protected $administeredSessionIds;

    /**
     * @var array
     */
    protected $instructedSessionIds;

    /**
     * @var array
     */
    protected $taughtCourseSchoolIds;

    /**
     * @var array
     */
    protected $directedProgramIds;

    /**
     * @var array
     */
    protected $directedProgramYearIds;

    /**
     * @var array
     */
    protected $directedProgramYearProgramIds;

    /**
     * @var array
     */
    protected $directedCohortIds;

    /**
     * @var array
     */
    protected $administeredCurriculumInventoryReportIds;

    /**
     * @var array
     */
    protected $administeredCurriculumInventoryReportSchoolIds;

    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @param IliosUserInterface $user
     * @param UserManager $userManager
     */
    public function __construct(IliosUserInterface $user, UserManager $userManager)
    {
        $this->userManager = $userManager;
        $relationships = $userManager->buildSessionRelationships($user->getId());
        $this->nonStudentSchoolIds = $relationships['nonStudentSchoolIds'];
        $this->directedCourseIds = $relationships['directedCourseIds'];
        $this->administeredCourseIds = $relationships['administeredCourseIds'];
        $this->directedSchoolIds = $relationships['directedSchoolIds'];
        $this->administeredSchoolIds = $relationships['administeredSchoolIds'];
        $this->directedCourseSchoolIds = $relationships['directedCourseSchoolIds'];
        $this->administeredCourseSchoolIds = $relationships['administeredCourseSchoolIds'];
        $this->administeredSessionSchoolIds = $relationships['administeredSessionSchoolIds'];
        $this->administeredSessionCourseIds = $relationships['administeredSessionCourseIds'];
        $this->taughtCourseIds = $relationships['taughtCourseIds'];
        $this->taughtCourseSchoolIds = $relationships['taughtCourseSchoolIds'];
        $this->administeredSessionIds = $relationships['administeredSessionIds'];
        $this->instructedSessionIds = $relationships['instructedSessionIds'];
        $this->directedProgramIds = $relationships['directedProgramIds'];
        $this->directedProgramYearIds = $relationships['directedProgramYearIds'];
        $this->directedProgramYearProgramIds = $relationships['directedProgramYearProgramIds'];
        $this->directedCohortIds = $relationships['directedCohortIds'];
        $this->administeredCurriculumInventoryReportIds
            = $relationships['administeredCurriculumInventoryReportIds'];
        $this->administeredCurriculumInventoryReportSchoolIds
            = $relationships['administeredCurriculumInventoryReportSchoolIds'];

        $this->userId = $user->getId();
        $this->isRoot = $user->isRoot();
        $this->isEnabled = $user->isEnabled();
        $this->schoolId = $user->getSchool()->getId();

        $authentication = $user->getAuthentication();
        if ($authentication) {
            $this->tokenNotValidBefore = $authentication->getInvalidateTokenIssuedBefore();
            $this->password = $authentication->getPassword();
            $this->isLegacyAccount = $authentication->isLegacyAccount();
        }
    }

    /**
     * @return bool
     */
    public function performsNonLearnerFunction():bool
    {
        $rhett = false;
        $props = [
            'directedCourseIds',
            'administeredCourseIds',
            'directedSchoolIds',
            'administeredSchoolIds',
            'taughtCourseIds',
            'administeredSessionIds',
            'instructedSessionIds',
            'directedProgramIds',
            'directedProgramYearIds',
            'directedCohortIds',
            'administeredCurriculumInventoryReportIds'
        ];
        foreach ($props as $prop) {
            if (! empty($this->$prop)) {
                $rhett = true;
                break;
            }
        }
        return $rhett;
    }

    /**
     * @inheritDoc
     */
    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof SessionUser) {
            return false;
        }

        if ($this->userId === $user->getUsername()) {
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function isTheUser(IliosUserInterface $user)
    {

        if ($this->userId === $user->getId()) {
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function isThePrimarySchool(SchoolInterface $school)
    {

        if ($this->schoolId === $school->getId()) {
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        return [];
    }

    /**
     * inheritdoc
     */
    public function getAssociatedSchoolIdsInNonLearnerFunction()
    {
        return $this->nonStudentSchoolIds;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->userId;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
        $this->password = null;
    }

    /**
     * @inheritdoc
     */
    public function isRoot()
    {
        return $this->isRoot;
    }

    /**
     * @inheritDoc
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * @inheritDoc
     */
    public function tokenNotValidBefore()
    {
        return $this->tokenNotValidBefore;
    }

    /**
     * @inheritDoc
     */
    public function getSchoolId()
    {
        return $this->schoolId;
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->userId;
    }

    /**
     * Use the old ilios legacy encoder for accounts
     * that haven't changed their password
     * @return string|null
     */
    public function getEncoderName()
    {
        if ($this->isLegacyAccount) {
            return 'ilios_legacy_encoder';
        }

        return null; // use the default encoder
    }

    /**
     * @inheritdoc
     */
    public function isDirectingCourse(int $courseId)
    {
        return in_array($courseId, $this->directedCourseIds);
    }

    public function isAdministeringCourse(int $courseId) : bool
    {
        return in_array($courseId, $this->administeredCourseIds);
    }

    public function isDirectingSchool(int $schoolId) : bool
    {
        return in_array($schoolId, $this->directedSchoolIds);
    }

    public function isAdministeringSchool(int $schoolId) : bool
    {
        return in_array($schoolId, $this->administeredSchoolIds);
    }

    public function isDirectingCourseInSchool(int $schoolId) : bool
    {
        return in_array($schoolId, $this->directedCourseSchoolIds);
    }

    public function isAdministeringCourseInSchool(int $schoolId) : bool
    {
        return in_array($schoolId, $this->administeredCourseSchoolIds);
    }

    public function isAdministeringSessionInSchool(int $schoolId) : bool
    {
        return in_array($schoolId, $this->administeredSessionSchoolIds);
    }

    public function isAdministeringSessionInCourse(int $courseId) : bool
    {
        return in_array($courseId, $this->administeredSessionCourseIds);
    }

    public function isTeachingCourseInSchool(int $schoolId) : bool
    {
        return in_array($schoolId, $this->taughtCourseSchoolIds);
    }

    public function isTeachingCourse(int $courseId) : bool
    {
        return in_array($courseId, $this->taughtCourseIds);
    }

    public function isAdministeringSession(int $sessionId) : bool
    {
        return in_array($sessionId, $this->administeredSessionIds);
    }

    public function isDirectingProgram(int $programId) : bool
    {
        return in_array($programId, $this->directedProgramIds);
    }

    public function isDirectingProgramYearInProgram(int $programId) : bool
    {
        return in_array($programId, $this->directedProgramYearProgramIds);
    }

    public function isDirectingCohort(int $cohortId) : bool
    {
        return in_array($cohortId, $this->directedCohortIds);
    }

    public function isDirectingProgramYear(int $programYearId) : bool
    {
        return in_array($programYearId, $this->directedProgramYearIds);
    }

    public function isTeachingSession(int $sessionId) : bool
    {
        return in_array($sessionId, $this->instructedSessionIds);
    }

    public function rolesInSchool(
        int $schoolId,
        $roles = [
            UserRoles::SCHOOL_DIRECTOR,
            UserRoles::SCHOOL_ADMINISTRATOR,
            UserRoles::COURSE_DIRECTOR,
            UserRoles::COURSE_ADMINISTRATOR,
            UserRoles::SESSION_ADMINISTRATOR,
            UserRoles::COURSE_INSTRUCTOR,
            UserRoles::CURRICULUM_INVENTORY_REPORT_ADMINISTRATOR
        ]
    ): array {
        $rhett = [];

        if (in_array(UserRoles::SCHOOL_DIRECTOR, $roles) && $this->isDirectingSchool($schoolId)) {
            $rhett[] = UserRoles::SCHOOL_DIRECTOR;
        }
        if (in_array(UserRoles::SCHOOL_ADMINISTRATOR, $roles) && $this->isAdministeringSchool($schoolId)) {
            $rhett[] = UserRoles::SCHOOL_ADMINISTRATOR;
        }
        if (in_array(UserRoles::COURSE_DIRECTOR, $roles) && $this->isDirectingCourseInSchool($schoolId)) {
            $rhett[] = UserRoles::COURSE_DIRECTOR;
        }
        if (in_array(UserRoles::COURSE_ADMINISTRATOR, $roles) &&
            $this->isAdministeringCourseInSchool($schoolId)) {
            $rhett[] = UserRoles::COURSE_ADMINISTRATOR;
        }
        if (in_array(UserRoles::SESSION_ADMINISTRATOR, $roles) &&
            $this->isAdministeringSessionInSchool($schoolId)) {
            $rhett[] = UserRoles::SESSION_ADMINISTRATOR;
        }
        if (in_array(UserRoles::COURSE_INSTRUCTOR, $roles) &&
            $this->isTeachingCourseInSchool($schoolId)) {
            $rhett[] = UserRoles::COURSE_INSTRUCTOR;
        }

        if (in_array(UserRoles::CURRICULUM_INVENTORY_REPORT_ADMINISTRATOR, $roles) &&
            $this->isAdministeringCurriculumInventoryReportInSchool($schoolId)) {
            $rhett[] = UserRoles::CURRICULUM_INVENTORY_REPORT_ADMINISTRATOR;
        }

        return $rhett;
    }

    public function rolesInCourse(
        int $courseId,
        $roles = [
            UserRoles::COURSE_DIRECTOR,
            UserRoles::COURSE_ADMINISTRATOR,
            UserRoles::SESSION_ADMINISTRATOR,
            UserRoles::COURSE_INSTRUCTOR
        ]
    ): array {
        $rhett = [];

        if (in_array(UserRoles::COURSE_DIRECTOR, $roles) && $this->isDirectingCourse($courseId)) {
            $rhett[] = UserRoles::COURSE_DIRECTOR;
        }
        if (in_array(UserRoles::COURSE_ADMINISTRATOR, $roles) && $this->isAdministeringCourse($courseId)) {
            $rhett[] = UserRoles::COURSE_ADMINISTRATOR;
        }
        if (in_array(UserRoles::SESSION_ADMINISTRATOR, $roles) &&
            $this->isAdministeringSessionInCourse($courseId)) {
            $rhett[] = UserRoles::SESSION_ADMINISTRATOR;
        }
        if (in_array(UserRoles::COURSE_INSTRUCTOR, $roles) && $this->isTeachingCourse($courseId)) {
            $rhett[] = UserRoles::COURSE_INSTRUCTOR;
        }

        return $rhett;
    }

    public function rolesInSession(
        int $sessionId,
        $roles = [UserRoles::SESSION_ADMINISTRATOR, UserRoles::SESSION_INSTRUCTOR]
    ): array {
        $rhett = [];

        if (in_array(UserRoles::SESSION_ADMINISTRATOR, $roles) && $this->isAdministeringSession($sessionId)) {
            $rhett[] = UserRoles::SESSION_ADMINISTRATOR;
        }
        if (in_array(UserRoles::SESSION_INSTRUCTOR, $roles) && $this->isTeachingSession($sessionId)) {
            $rhett[] = UserRoles::SESSION_INSTRUCTOR;
        }

        return $rhett;
    }

    public function rolesInProgram(
        int $programId,
        $roles = [UserRoles::PROGRAM_DIRECTOR, UserRoles::PROGRAM_YEAR_DIRECTOR]
    ): array {
        $rhett = [];

        if (in_array(UserRoles::PROGRAM_DIRECTOR, $roles) && $this->isDirectingProgram($programId)) {
            $rhett[] = UserRoles::PROGRAM_DIRECTOR;
        }
        if (in_array(UserRoles::PROGRAM_YEAR_DIRECTOR, $roles) &&
            $this->isDirectingProgramYearInProgram($programId)) {
            $rhett[] = UserRoles::PROGRAM_YEAR_DIRECTOR;
        }

        return $rhett;
    }

    public function rolesInProgramYear(int $programYearId, $roles = [UserRoles::PROGRAM_YEAR_DIRECTOR]) : array
    {
        $rhett = [];

        if (in_array(UserRoles::PROGRAM_YEAR_DIRECTOR, $roles) &&
            $this->isDirectingProgramYear($programYearId)) {
            $rhett[] = UserRoles::PROGRAM_YEAR_DIRECTOR;
        }

        return $rhett;
    }

    public function rolesInCohort(int $cohortId, $roles = [UserRoles::PROGRAM_YEAR_DIRECTOR]) : array
    {
        $rhett = [];

        if (in_array(UserRoles::PROGRAM_YEAR_DIRECTOR, $roles) && $this->isDirectingCohort($cohortId)) {
            $rhett[] = UserRoles::PROGRAM_YEAR_DIRECTOR;
        }

        return $rhett;
    }

    public function isAdministeringCurriculumInventoryReportInSchool(int $schoolId) : bool
    {
        return in_array($schoolId, $this->administeredCurriculumInventoryReportSchoolIds);
    }

    public function isAdministeringCurriculumInventoryReport(int $curriculumInventoryReportId): bool
    {
        return in_array($curriculumInventoryReportId, $this->administeredCurriculumInventoryReportIds);
    }

    public function rolesInCurriculumInventoryReport(
        int $curriculumInventoryReportId,
        $roles = [UserRoles::CURRICULUM_INVENTORY_REPORT_ADMINISTRATOR]
    ): array {
        $rhett = [];

        if (in_array(UserRoles::CURRICULUM_INVENTORY_REPORT_ADMINISTRATOR, $roles) &&
            $this->isAdministeringCurriculumInventoryReport($curriculumInventoryReportId)) {
            $rhett[] = UserRoles::CURRICULUM_INVENTORY_REPORT_ADMINISTRATOR;
        }

        return $rhett;
    }
}
