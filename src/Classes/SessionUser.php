<?php

namespace App\Classes;

use App\Entity\Manager\UserManager;
use App\Entity\SchoolInterface;
use Exception;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\UserInterface as IliosUserInterface;
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
    protected $instructedOfferingIlmSessionCourseAndSchoolIds;

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
     * @inheritdoc
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
     */
    public function isDirectingCourse(int $courseId)
    {
        return in_array($courseId, $this->getDirectedCourseIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isAdministeringCourse(int $courseId) : bool
    {
        return in_array($courseId, $this->getAdministeredCourseIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isDirectingSchool(int $schoolId) : bool
    {
        return in_array($schoolId, $this->getDirectedSchoolIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isAdministeringSchool(int $schoolId) : bool
    {
        return in_array($schoolId, $this->getAdministeredSchoolIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isDirectingCourseInSchool(int $schoolId) : bool
    {
        return in_array($schoolId, $this->getDirectedCourseSchoolIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isAdministeringCourseInSchool(int $schoolId) : bool
    {
        return in_array($schoolId, $this->getAdministeredCourseSchoolIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isAdministeringSessionInSchool(int $schoolId) : bool
    {
        return in_array($schoolId, $this->getAdministeredSessionSchoolIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isAdministeringSessionInCourse(int $courseId) : bool
    {
        return in_array($courseId, $this->getAdministeredSessionCourseIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isTeachingCourseInSchool(int $schoolId) : bool
    {
        return in_array($schoolId, $this->getTaughtCourseSchoolIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isTeachingCourse(int $courseId) : bool
    {
        return in_array($courseId, $this->getTaughtCourseIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isAdministeringSession(int $sessionId) : bool
    {
        return in_array($sessionId, $this->getAdministeredSessionIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isDirectingProgram(int $programId) : bool
    {
        return in_array($programId, $this->getDirectedProgramIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isDirectingProgramInSchool(int $schoolId) : bool
    {
        return in_array($schoolId, $this->getDirectedProgramSchoolIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isDirectingProgramYearInProgram(int $programId) : bool
    {
        return in_array($programId, $this->getDirectedProgramYearProgramIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isDirectingProgramYear(int $programYearId) : bool
    {
        return in_array($programYearId, $this->getDirectedProgramYearIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isTeachingSession(int $sessionId) : bool
    {
        return in_array($sessionId, $this->getInstructedSessionIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isInstructingOffering(int $offeringId) : bool
    {
        return in_array($offeringId, $this->getInstructedOfferingIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isInstructingIlm(int $ilmId) : bool
    {
        return in_array($ilmId, $this->getInstructedIlmIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
     */
    public function isAdministeringCurriculumInventoryReportInSchool(int $schoolId) : bool
    {
        return in_array($schoolId, $this->getAdministeredCurriculumInventoryReportSchoolIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isAdministeringCurriculumInventoryReport(int $curriculumInventoryReportId): bool
    {
        return in_array($curriculumInventoryReportId, $this->getAdministeredCurriculumInventoryReportIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
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
     * @throws Exception
     */
    public function getDirectedCourseIds(): array
    {
        return $this->getDirectedCourseAndSchoolIds()['courseIds'];
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function getAdministeredCourseIds(): array
    {
        return $this->getAdministeredCourseAndSchoolIds()['courseIds'];
    }

    /**
     * @inheritdoc
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
     */
    public function getDirectedCourseSchoolIds(): array
    {
        return $this->getDirectedCourseAndSchoolIds()['schoolIds'];
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function getAdministeredCourseSchoolIds(): array
    {
        return $this->getAdministeredCourseAndSchoolIds()['schoolIds'];
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function getAdministeredSessionSchoolIds(): array
    {
        return $this->getAdministeredSessionCourseAndSchoolIds()['schoolIds'];
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function getAdministeredSessionCourseIds(): array
    {
        return $this->getAdministeredSessionCourseAndSchoolIds()['courseIds'];
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function getTaughtCourseIds(): array
    {
        return $this->getInstructedOfferingIlmSessionCourseAndSchoolIds()['courseIds'];
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function getAdministeredSessionIds(): array
    {
        return $this->getAdministeredSessionCourseAndSchoolIds()['sessionIds'];
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function getInstructedSessionIds(): array
    {
        return $this->getInstructedOfferingIlmSessionCourseAndSchoolIds()['sessionIds'];
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function getInstructedIlmIds(): array
    {
        return $this->getInstructedOfferingIlmSessionCourseAndSchoolIds()['ilmIds'];
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function getInstructedOfferingIds(): array
    {
        return $this->getInstructedOfferingIlmSessionCourseAndSchoolIds()['offeringIds'];
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function getTaughtCourseSchoolIds(): array
    {
        return $this->getInstructedOfferingIlmSessionCourseAndSchoolIds()['schoolIds'];
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function getDirectedProgramIds(): array
    {
        return $this->getDirectedProgramAndSchoolIds()['programIds'];
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function getDirectedProgramSchoolIds(): array
    {
        return $this->getDirectedProgramAndSchoolIds()['schoolIds'];
    }

    /**
     * @@inheritdoc
     * @throws Exception
     */
    public function getDirectedProgramYearIds(): array
    {
        return $this->getDirectedProgramYearProgramAndSchoolIds()['programYearIds'];
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function getDirectedProgramYearProgramIds(): array
    {
        return $this->getDirectedProgramYearProgramAndSchoolIds()['programIds'];
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function getAdministeredCurriculumInventoryReportIds(): array
    {
        return $this->getAdministeredCurriculumInventoryReportAndSchoolIds()['reportIds'];
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function getAdministeredCurriculumInventoryReportSchoolIds(): array
    {
        return $this->getAdministeredCurriculumInventoryReportAndSchoolIds()['schoolIds'];
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isInLearnerGroup(int $learnerGroupId): bool
    {
        $ids = $this->getLearnerGroupIds();
        return in_array($learnerGroupId, $ids);
    }

    /**
     * @return array
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
     */
    protected function getInstructedOfferingIlmSessionCourseAndSchoolIds(): array
    {
        if (!isset($this->instructedOfferingIlmSessionCourseAndSchoolIds)) {
            $this->instructedOfferingIlmSessionCourseAndSchoolIds =
                $this->userManager->getInstructedOfferingIlmSessionCourseAndSchoolIds($this->getId());
        }
        return $this->instructedOfferingIlmSessionCourseAndSchoolIds;
    }

    /**
     * @return array
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
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
