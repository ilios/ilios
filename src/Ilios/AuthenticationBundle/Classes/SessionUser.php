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
    protected $directedCourseAndSchoolIds;

    /**
     * @var array
     */
    protected $administeredCourseAndSchoolIds;

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
    protected $administeredSessionCourseAndSchoolIds;

    /**
     * @var array
     */
    protected $instructedSessionCourseAndSchoolIds;

    /**
     * @var array
     */
    protected $instructedLearnerGroupSchoolIds;

    /**
     * @var array
     */
    protected $directedProgramAndSchoolIds;

    /**
     * @var array
     */
    protected $directedProgramYearProgramAndSchoolIds;

    /**
     * @var array
     */
    protected $administeredCurriculumInventoryReportAndSchoolIds;

    /**
     * @var array
     */
    protected $learnerGroupIds;

    /**
     * @var array
     */
    protected $instructorGroupIds;

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
        return
            $this->isRoot() ||
            !empty($this->getDirectedCourseIds()) ||
            !empty($this->getAdministeredCourseIds()) ||
            !empty($this->getDirectedSchoolIds()) ||
            !empty($this->getAdministeredSchoolIds()) ||
            !empty($this->getInstructorGroupIds()) ||
            !empty($this->getTaughtCourseIds()) ||
            !empty($this->getAdministeredSessionIds()) ||
            !empty($this->getInstructedSessionIds()) ||
            !empty($this->getDirectedProgramIds()) ||
            !empty($this->getDirectedProgramYearIds()) ||
            !empty($this->getAdministeredCurriculumInventoryReportIds());
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function isTheUser(IliosUserInterface $user)
    {

        if ($this->userId === $user->getId()) {
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function isThePrimarySchool(SchoolInterface $school)
    {

        if ($this->schoolId === $school->getId()) {
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function getRoles()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAssociatedSchoolIdsInNonLearnerFunction()
    {
        return array_merge(
            $this->getDirectedSchoolIds(),
            $this->getAdministeredSchoolIds(),
            $this->getDirectedCourseSchoolIds(),
            $this->getAdministeredCourseSchoolIds(),
            $this->getAdministeredSessionSchoolIds(),
            $this->getTaughtCourseSchoolIds(),
            $this->getInstructedLearnerGroupSchoolIds(),
            $this->getInstructorGroupSchoolIds(),
            $this->getDirectedProgramAndSchoolIds()['schoolIds'],
            $this->getDirectedProgramYearProgramAndSchoolIds()['schoolIds']
        );
    }

    /**
     * @inheritdoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @inheritdoc
     */
    public function getSalt()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function getUsername()
    {
        return $this->userId;
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * @inheritdoc
     */
    public function tokenNotValidBefore()
    {
        return $this->tokenNotValidBefore;
    }

    /**
     * @inheritdoc
     */
    public function getSchoolId()
    {
        return $this->schoolId;
    }

    /**
     * @inheritdoc
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
        return in_array($courseId, $this->getDirectedCourseIds());
    }

    /**
     * @inheritdoc
     */
    public function isAdministeringCourse(int $courseId) : bool
    {
        return in_array($courseId, $this->getAdministeredCourseIds());
    }

    /**
     * @inheritdoc
     */
    public function isDirectingSchool(int $schoolId) : bool
    {
        return in_array($schoolId, $this->getDirectedSchoolIds());
    }

    /**
     * @inheritdoc
     */
    public function isAdministeringSchool(int $schoolId) : bool
    {
        return in_array($schoolId, $this->getAdministeredSchoolIds());
    }

    /**
     * @inheritdoc
     */
    public function isDirectingCourseInSchool(int $schoolId) : bool
    {
        return in_array($schoolId, $this->getDirectedCourseSchoolIds());
    }

    /**
     * @inheritdoc
     */
    public function isAdministeringCourseInSchool(int $schoolId) : bool
    {
        return in_array($schoolId, $this->getAdministeredCourseSchoolIds());
    }

    /**
     * @inheritdoc
     */
    public function isAdministeringSessionInSchool(int $schoolId) : bool
    {
        return in_array($schoolId, $this->getAdministeredSessionSchoolIds());
    }

    /**
     * @inheritdoc
     */
    public function isAdministeringSessionInCourse(int $courseId) : bool
    {
        return in_array($courseId, $this->getAdministeredSessionCourseIds());
    }

    /**
     * @inheritdoc
     */
    public function isTeachingCourseInSchool(int $schoolId) : bool
    {
        return in_array($schoolId, $this->getTaughtCourseSchoolIds());
    }

    /**
     * @inheritdoc
     */
    public function isTeachingCourse(int $courseId) : bool
    {
        return in_array($courseId, $this->getTaughtCourseIds());
    }

    /**
     * @inheritdoc
     */
    public function isAdministeringSession(int $sessionId) : bool
    {
        return in_array($sessionId, $this->getAdministeredSessionIds());
    }

    /**
     * @inheritdoc
     */
    public function isDirectingProgram(int $programId) : bool
    {
        return in_array($programId, $this->getDirectedProgramIds());
    }

    /**
     * @inheritdoc
     */
    public function isDirectingProgramInSchool(int $schoolId) : bool
    {
        return in_array($schoolId, $this->getDirectedProgramSchoolIds());
    }

    /**
     * @inheritdoc
     */
    public function isDirectingProgramYearInProgram(int $programId) : bool
    {
        return in_array($programId, $this->getDirectedProgramYearProgramIds());
    }

    /**
     * @inheritdoc
     */
    public function isDirectingProgramYear(int $programYearId) : bool
    {
        return in_array($programYearId, $this->getDirectedProgramYearIds());
    }

    /**
     * @inheritdoc
     */
    public function isTeachingSession(int $sessionId) : bool
    {
        return in_array($sessionId, $this->getInstructedSessionIds());
    }

    /**
     * @inheritdoc
     */
    public function rolesInSchool(
        int $schoolId,
        $roles = [
            UserRoles::SCHOOL_DIRECTOR,
            UserRoles::SCHOOL_ADMINISTRATOR,
            UserRoles::COURSE_DIRECTOR,
            UserRoles::COURSE_ADMINISTRATOR,
            UserRoles::SESSION_ADMINISTRATOR,
            UserRoles::COURSE_INSTRUCTOR,
            UserRoles::CURRICULUM_INVENTORY_REPORT_ADMINISTRATOR,
            UserRoles::PROGRAM_DIRECTOR,
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
        if (in_array(UserRoles::PROGRAM_DIRECTOR, $roles) &&
            $this->isDirectingProgramInSchool($schoolId)) {
            $rhett[] = UserRoles::PROGRAM_DIRECTOR;
        }

        return $rhett;
    }

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
    public function rolesInProgramYear(int $programYearId, $roles = [UserRoles::PROGRAM_YEAR_DIRECTOR]) : array
    {
        $rhett = [];

        if (in_array(UserRoles::PROGRAM_YEAR_DIRECTOR, $roles) &&
            $this->isDirectingProgramYear($programYearId)) {
            $rhett[] = UserRoles::PROGRAM_YEAR_DIRECTOR;
        }

        return $rhett;
    }

    /**
     * @inheritdoc
     */
    public function isAdministeringCurriculumInventoryReportInSchool(int $schoolId) : bool
    {
        return in_array($schoolId, $this->getAdministeredCurriculumInventoryReportSchoolIds());
    }

    /**
     * @inheritdoc
     */
    public function isAdministeringCurriculumInventoryReport(int $curriculumInventoryReportId): bool
    {
        return in_array($curriculumInventoryReportId, $this->getAdministeredCurriculumInventoryReportIds());
    }

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
    public function getDirectedCourseIds(): array
    {
        return $this->getDirectedCourseAndSchoolIds()['courseIds'];
    }

    /**
     * @inheritdoc
     */
    public function getAdministeredCourseIds(): array
    {
        return $this->getAdministeredCourseAndSchoolIds()['courseIds'];
    }

    /**
     * @inheritdoc
     */
    public function getDirectedSchoolIds(): array
    {
        if (!isset($this->directedSchoolIds)) {
            $this->directedSchoolIds = $this->userManager->getDirectedSchoolIds($this->getId());
        }
        return $this->directedSchoolIds;
    }

    /**
     * @inheritdoc
     */
    public function getAdministeredSchoolIds(): array
    {
        if (!isset($this->administeredSchoolIds)) {
            $this->administeredSchoolIds = $this->userManager->getAdministeredSchoolIds($this->getId());
        }
        return $this->administeredSchoolIds;
    }

    /**
     * @inheritdoc
     */
    public function getDirectedCourseSchoolIds(): array
    {
        return $this->getDirectedCourseAndSchoolIds()['schoolIds'];
    }

    /**
     * @inheritdoc
     */
    public function getAdministeredCourseSchoolIds(): array
    {
        return $this->getAdministeredCourseAndSchoolIds()['schoolIds'];
    }

    /**
     * @inheritdoc
     */
    public function getAdministeredSessionSchoolIds(): array
    {
        return $this->getAdministeredSessionCourseAndSchoolIds()['schoolIds'];
    }

    /**
     * @inheritdoc
     */
    public function getAdministeredSessionCourseIds(): array
    {
        return $this->getAdministeredSessionCourseAndSchoolIds()['courseIds'];
    }

    /**
     * @inheritdoc
     */
    public function getTaughtCourseIds(): array
    {
        return $this->getInstructedSessionCourseAndSchoolIds()['courseIds'];
    }

    /**
     * @inheritdoc
     */
    public function getAdministeredSessionIds(): array
    {
        return $this->getAdministeredSessionCourseAndSchoolIds()['sessionIds'];
    }

    /**
     * @inheritdoc
     */
    public function getInstructedSessionIds(): array
    {
        return $this->getInstructedSessionCourseAndSchoolIds()['sessionIds'];
    }

    /**
     * @inheritdoc
     */
    public function getTaughtCourseSchoolIds(): array
    {
        return $this->getInstructedSessionCourseAndSchoolIds()['schoolIds'];
    }

    /**
     * @inheritdoc
     */
    public function getDirectedProgramIds(): array
    {
        return $this->getDirectedProgramAndSchoolIds()['programIds'];
    }

    /**
     * @inheritdoc
     */
    public function getDirectedProgramSchoolIds(): array
    {
        return $this->getDirectedProgramAndSchoolIds()['schoolIds'];
    }

    /**
     * @@inheritdoc
     */
    public function getDirectedProgramYearIds(): array
    {
        return $this->getDirectedProgramYearProgramAndSchoolIds()['programYearIds'];
    }

    /**
     * @inheritdoc
     */
    public function getDirectedProgramYearProgramIds(): array
    {
        return $this->getDirectedProgramYearProgramAndSchoolIds()['programIds'];
    }

    /**
     * @inheritdoc
     */
    public function getAdministeredCurriculumInventoryReportIds(): array
    {
        return $this->getAdministeredCurriculumInventoryReportAndSchoolIds()['reportIds'];
    }

    /**
     * @inheritdoc
     */
    public function getAdministeredCurriculumInventoryReportSchoolIds(): array
    {
        return $this->getAdministeredCurriculumInventoryReportAndSchoolIds()['schoolIds'];
    }

    /**
     * @inheritdoc
     */
    public function isInLearnerGroup(int $learnerGroupId): bool
    {
        $ids = $this->getLearnerGroupIds();
        return in_array($learnerGroupId, $ids);
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function getDirectedProgramYearProgramAndSchoolIds(): array
    {
        if (!isset($this->directedProgramYearProgramAndSchoolIds)) {
            $this->directedProgramYearProgramAndSchoolIds =
                $this->userManager->getDirectedProgramYearProgramAndSchoolIds($this->getId());
        }

        return $this->directedProgramYearProgramAndSchoolIds;
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function getAdministeredCurriculumInventoryReportAndSchoolIds()
    {
        if (!isset($this->administeredCurriculumInventoryReportSchoolIds)) {
            $this->administeredCurriculumInventoryReportAndSchoolIds =
                $this->userManager->getAdministeredCurriculumInventoryReportAndSchoolIds($this->getId());
        }
        return $this->administeredCurriculumInventoryReportAndSchoolIds;
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function getInstructedSessionCourseAndSchoolIds(): array
    {
        if (!isset($this->instructedSessionCourseAndSchoolIds)) {
            $this->instructedSessionCourseAndSchoolIds =
                $this->userManager->getInstructedSessionCourseAndSchoolIds($this->getId());
        }
        return $this->instructedSessionCourseAndSchoolIds;
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function getDirectedProgramAndSchoolIds(): array
    {
        if (!isset($this->directedProgramAndSchoolIds)) {
            $this->directedProgramAndSchoolIds = $this->userManager->getDirectedProgramAndSchoolIds($this->getId());
        }
        return $this->directedProgramAndSchoolIds;
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function getAdministeredSessionCourseAndSchoolIds(): array
    {
        if (!isset($this->administeredSessionCourseAndSchoolIds)) {
            $this->administeredSessionCourseAndSchoolIds =
                $this->userManager->getAdministeredSessionCourseAndSchoolIds($this->getId());
        }
        return $this->administeredSessionCourseAndSchoolIds;
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function getAdministeredCourseAndSchoolIds(): array
    {
        if (!isset($this->administeredCourseAndSchoolIds)) {
            $this->administeredCourseAndSchoolIds = $this->userManager->getAdministeredCourseAndSchoolIds(
                $this->getId()
            );
        }
        return $this->administeredCourseAndSchoolIds;
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function getDirectedCourseAndSchoolIds(): array
    {
        if (!isset($this->directedCourseAndSchoolIds)) {
            $this->directedCourseAndSchoolIds = $this->userManager->getDirectedCourseAndSchoolIds($this->getId());
        }
        return $this->directedCourseAndSchoolIds;
    }

    /**
     * @return array
     * @throws \Exception
     * @see UserManager::getInstructedLearnerGroupSchoolIds()
     */
    protected function getInstructedLearnerGroupSchoolIds(): array
    {
        if (!isset($this->instructedLearnerGroupSchoolIds)) {
            $this->instructedLearnerGroupSchoolIds =
                $this->userManager->getInstructedLearnerGroupSchoolIds($this->getId());
        }
        return $this->instructedLearnerGroupSchoolIds;
    }

    /**
     * @return array
     * @throws \Exception
     * @see UserManager::getInstructorGroupSchoolIds()
     */
    protected function getInstructorGroupSchoolIds(): array
    {
        if (!isset($this->instructorGroupSchoolIds)) {
            $this->instructorGroupSchoolIds =
                $this->userManager->getInstructorGroupSchoolIds($this->getId());
        }
        return $this->instructorGroupSchoolIds;
    }

    /**
     * @return array
     * @throws \Exception
     * @see UserManager::getLearnerGroupIds()
     */
    protected function getLearnerGroupIds(): array
    {
        if (!isset($this->learnerGroupIds)) {
            $this->learnerGroupIds =
                $this->userManager->getLearnerGroupIds($this->getId());
        }
        return $this->learnerGroupIds;
    }

    /**
     * @return array
     * @throws \Exception
     * @see UserManager::getInstructorGroupIds()
     */
    protected function getInstructorGroupIds(): array
    {
        if (!isset($this->instructorGroupIds)) {
            $this->instructorGroupIds =
                $this->userManager->getInstructorGroupIds($this->getId());
        }
        return $this->instructorGroupIds;
    }
}
