<?php

declare(strict_types=1);

namespace App\Classes;

use App\Entity\SchoolInterface;
use App\Repository\UserRepository;
use Exception;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\UserInterface as IliosUserInterface;
use DateTime;

/**
 * Class SessionUser
 * A session user is a static serializable representation
 * of a single user.  It is used in our authentication system
 * to avoid issues with a user being able to update their own data.
 */
class SessionUser implements SessionUserInterface
{
    protected int $userId;
    protected bool $isRoot;
    protected bool $isEnabled;
    protected int $schoolId;
    protected ?DateTime $tokenNotValidBefore = null;
    protected ?string $password;
    protected array $directedCourseAndSchoolIds;
    protected array $administeredCourseAndSchoolIds;
    protected array $directedSchoolIds;
    protected array $administeredSchoolIds;
    protected array $administeredSessionCourseAndSchoolIds;
    protected array $studentAdvisedSessionAndCourseIds;
    protected array $instructedOfferingIlmSessionCourseAndSchoolIds;
    protected array $instructedLearnerGroupSchoolIds;
    protected array $instructorGroupSchoolIds;
    protected array $directedProgramAndSchoolIds;
    protected array $directedProgramYearProgramAndSchoolIds;
    protected array $administeredCurriculumInventoryReportAndSchoolIds;
    protected array $learnerGroupIds;
    protected array $instructorGroupIds;
    protected array $coursesCohortsProgramYearAndProgramIdsLinkedToProgramsDirectedByUser;
    protected array $learnerIlmAndOfferingIds;
    protected array $learnerSessionIds;
    protected UserRepository $userRepository;

    public function __construct(IliosUserInterface $user, UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;

        $this->userId = $user->getId();
        $this->isRoot = $user->isRoot();
        $this->isEnabled = $user->isEnabled();
        $this->schoolId = $user->getSchool()->getId();

        $authentication = $user->getAuthentication();
        if ($authentication) {
            $this->tokenNotValidBefore = $authentication->getInvalidateTokenIssuedBefore();
            $this->password = $authentication->getPassword();
        }
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function performsNonLearnerFunction(): bool
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

    public function isEqualTo(UserInterface $user): bool
    {
        if (!$user instanceof SessionUser) {
            return false;
        }

        return $this->getUserIdentifier() === $user->getUserIdentifier();
    }

    public function isTheUser(IliosUserInterface $user): bool
    {

        if ($this->userId === $user->getId()) {
            return true;
        }

        return false;
    }

    public function isThePrimarySchool(SchoolInterface $school): bool
    {

        if ($this->schoolId === $school->getId()) {
            return true;
        }

        return false;
    }

    public function getRoles(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function getAssociatedSchoolIdsInNonLearnerFunction(): array
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->userId;
    }

    /**
     * This method is deprecated. See __serialize() instead.
     */
    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // not implemented.
    }

    public function isRoot(): bool
    {
        return $this->isRoot;
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function tokenNotValidBefore(): ?DateTime
    {
        return $this->tokenNotValidBefore;
    }

    public function getSchoolId(): int
    {
        return $this->schoolId;
    }

    public function getId(): int
    {
        return $this->userId;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isDirectingCourse(int $courseId): bool
    {
        return in_array($courseId, $this->getDirectedCourseIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isAdministeringCourse(int $courseId): bool
    {
        return in_array($courseId, $this->getAdministeredCourseIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isDirectingSchool(int $schoolId): bool
    {
        return in_array($schoolId, $this->getDirectedSchoolIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isAdministeringSchool(int $schoolId): bool
    {
        return in_array($schoolId, $this->getAdministeredSchoolIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isDirectingCourseInSchool(int $schoolId): bool
    {
        return in_array($schoolId, $this->getDirectedCourseSchoolIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isAdministeringCourseInSchool(int $schoolId): bool
    {
        return in_array($schoolId, $this->getAdministeredCourseSchoolIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isAdministeringSessionInSchool(int $schoolId): bool
    {
        return in_array($schoolId, $this->getAdministeredSessionSchoolIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isAdministeringSessionInCourse(int $courseId): bool
    {
        return in_array($courseId, $this->getAdministeredSessionCourseIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isTeachingCourseInSchool(int $schoolId): bool
    {
        return in_array($schoolId, $this->getTaughtCourseSchoolIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isTeachingCourse(int $courseId): bool
    {
        return in_array($courseId, $this->getTaughtCourseIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isAdministeringSession(int $sessionId): bool
    {
        return in_array($sessionId, $this->getAdministeredSessionIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isDirectingProgram(int $programId): bool
    {
        return in_array($programId, $this->getDirectedProgramIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isDirectingProgramInSchool(int $schoolId): bool
    {
        return in_array($schoolId, $this->getDirectedProgramSchoolIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isDirectingProgramYearInProgram(int $programId): bool
    {
        return in_array($programId, $this->getDirectedProgramYearProgramIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isDirectingProgramYear(int $programYearId): bool
    {
        return in_array($programYearId, $this->getDirectedProgramYearIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isTeachingSession(int $sessionId): bool
    {
        return in_array($sessionId, $this->getInstructedSessionIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isInstructingOffering(int $offeringId): bool
    {
        return in_array($offeringId, $this->getInstructedOfferingIds());
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isInstructingIlm(int $ilmId): bool
    {
        return in_array($ilmId, $this->getInstructedIlmIds());
    }

    public function isStudentAdvisorInSession(int $sessionId): bool
    {
        return in_array($sessionId, $this->getStudentAdvisedSessionIds());
    }

    public function isStudentAdvisorInCourse(int $courseId): bool
    {
        return in_array($courseId, $this->getStudentAdvisedCourseIds());
    }

    public function isLearnerInOffering(int $offeringId): bool
    {
        return in_array($offeringId, $this->getLearnerOfferingsIds());
    }

    public function isLearnerInSession(int $sessionId): bool
    {
        return in_array($sessionId, $this->getLearnerSessionIds());
    }

    public function isLearnerInIlm(int $ilmId): bool
    {
        return in_array($ilmId, $this->getLearnerIlmIds());
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
        if (
            in_array(UserRoles::COURSE_ADMINISTRATOR, $roles) &&
            $this->isAdministeringCourseInSchool($schoolId)
        ) {
            $rhett[] = UserRoles::COURSE_ADMINISTRATOR;
        }
        if (
            in_array(UserRoles::SESSION_ADMINISTRATOR, $roles) &&
            $this->isAdministeringSessionInSchool($schoolId)
        ) {
            $rhett[] = UserRoles::SESSION_ADMINISTRATOR;
        }
        if (
            in_array(UserRoles::COURSE_INSTRUCTOR, $roles) &&
            $this->isTeachingCourseInSchool($schoolId)
        ) {
            $rhett[] = UserRoles::COURSE_INSTRUCTOR;
        }
        if (
            in_array(UserRoles::CURRICULUM_INVENTORY_REPORT_ADMINISTRATOR, $roles) &&
            $this->isAdministeringCurriculumInventoryReportInSchool($schoolId)
        ) {
            $rhett[] = UserRoles::CURRICULUM_INVENTORY_REPORT_ADMINISTRATOR;
        }
        if (
            in_array(UserRoles::PROGRAM_DIRECTOR, $roles) &&
            $this->isDirectingProgramInSchool($schoolId)
        ) {
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
            UserRoles::COURSE_INSTRUCTOR,
        ]
    ): array {
        $rhett = [];

        if (in_array(UserRoles::COURSE_DIRECTOR, $roles) && $this->isDirectingCourse($courseId)) {
            $rhett[] = UserRoles::COURSE_DIRECTOR;
        }
        if (in_array(UserRoles::COURSE_ADMINISTRATOR, $roles) && $this->isAdministeringCourse($courseId)) {
            $rhett[] = UserRoles::COURSE_ADMINISTRATOR;
        }
        if (
            in_array(UserRoles::SESSION_ADMINISTRATOR, $roles) &&
            $this->isAdministeringSessionInCourse($courseId)
        ) {
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
        if (
            in_array(UserRoles::PROGRAM_YEAR_DIRECTOR, $roles) &&
            $this->isDirectingProgramYearInProgram($programId)
        ) {
            $rhett[] = UserRoles::PROGRAM_YEAR_DIRECTOR;
        }

        return $rhett;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function rolesInProgramYear(int $programYearId, $roles = [UserRoles::PROGRAM_YEAR_DIRECTOR]): array
    {
        $rhett = [];

        if (
            in_array(UserRoles::PROGRAM_YEAR_DIRECTOR, $roles) &&
            $this->isDirectingProgramYear($programYearId)
        ) {
            $rhett[] = UserRoles::PROGRAM_YEAR_DIRECTOR;
        }

        return $rhett;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isAdministeringCurriculumInventoryReportInSchool(int $schoolId): bool
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

        if (
            in_array(UserRoles::CURRICULUM_INVENTORY_REPORT_ADMINISTRATOR, $roles) &&
            $this->isAdministeringCurriculumInventoryReport($curriculumInventoryReportId)
        ) {
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
            $this->directedSchoolIds = $this->userRepository->getDirectedSchoolIds($this->getId());
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
            $this->administeredSchoolIds = $this->userRepository->getAdministeredSchoolIds($this->getId());
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

    protected function getStudentAdvisedCourseIds(): array
    {
        return $this->getStudentAdvisedSessionAndCourseIds()['courseIds'];
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

    protected function getStudentAdvisedSessionIds(): array
    {
        return $this->getStudentAdvisedSessionAndCourseIds()['sessionIds'];
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
     * @inheritdoc
     * @throws Exception
     */
    public function getCourseIdsLinkedToProgramsDirectedByUser(): array
    {
        return $this->getCoursesCohortsProgramYearAndProgramIdsLinkedToProgramsDirectedByUser()['courseIds'];
    }

    protected function getLearnerOfferingsIds(): array
    {
        return $this->getLearnerIlmAndOfferingIds()['offeringIds'];
    }

    protected function getLearnerIlmIds(): array
    {
        return $this->getLearnerIlmAndOfferingIds()['ilmIds'];
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function isDirectingProgramLinkedToCourse(int $courseId): bool
    {
        $ids = $this->getCourseIdsLinkedToProgramsDirectedByUser();
        return in_array($courseId, $ids);
    }

    /**
     * @throws Exception
     */
    protected function getDirectedProgramYearProgramAndSchoolIds(): array
    {
        if (!isset($this->directedProgramYearProgramAndSchoolIds)) {
            $this->directedProgramYearProgramAndSchoolIds =
                $this->userRepository->getDirectedProgramYearProgramAndSchoolIds($this->getId());
        }

        return $this->directedProgramYearProgramAndSchoolIds;
    }

    /**
     * @throws Exception
     */
    protected function getAdministeredCurriculumInventoryReportAndSchoolIds(): array
    {
        if (!isset($this->administeredCurriculumInventoryReportSchoolIds)) {
            $this->administeredCurriculumInventoryReportAndSchoolIds =
                $this->userRepository->getAdministeredCurriculumInventoryReportAndSchoolIds($this->getId());
        }
        return $this->administeredCurriculumInventoryReportAndSchoolIds;
    }

    /**
     * @throws Exception
     */
    protected function getInstructedOfferingIlmSessionCourseAndSchoolIds(): array
    {
        if (!isset($this->instructedOfferingIlmSessionCourseAndSchoolIds)) {
            $this->instructedOfferingIlmSessionCourseAndSchoolIds =
                $this->userRepository->getInstructedOfferingIlmSessionCourseAndSchoolIds($this->getId());
        }
        return $this->instructedOfferingIlmSessionCourseAndSchoolIds;
    }

    /**
     * @throws Exception
     */
    protected function getDirectedProgramAndSchoolIds(): array
    {
        if (!isset($this->directedProgramAndSchoolIds)) {
            $this->directedProgramAndSchoolIds = $this->userRepository->getDirectedProgramAndSchoolIds($this->getId());
        }
        return $this->directedProgramAndSchoolIds;
    }

    /**
     * @throws Exception
     */
    protected function getCoursesCohortsProgramYearAndProgramIdsLinkedToProgramsDirectedByUser(): array
    {
        if (!isset($this->coursesCohortsProgramYearAndProgramIdsLinkedToProgramsDirectedByUser)) {
            $this->coursesCohortsProgramYearAndProgramIdsLinkedToProgramsDirectedByUser
                = $this->userRepository
                ->getCoursesCohortsProgramYearAndProgramIdsLinkedToProgramsDirectedByUser($this->getId());
        }
        return $this->coursesCohortsProgramYearAndProgramIdsLinkedToProgramsDirectedByUser;
    }

    /**
     * @throws Exception
     */
    protected function getAdministeredSessionCourseAndSchoolIds(): array
    {
        if (!isset($this->administeredSessionCourseAndSchoolIds)) {
            $this->administeredSessionCourseAndSchoolIds =
                $this->userRepository->getAdministeredSessionCourseAndSchoolIds($this->getId());
        }
        return $this->administeredSessionCourseAndSchoolIds;
    }

    /**
     * @throws Exception
     */
    protected function getStudentAdvisedSessionAndCourseIds(): array
    {
        if (!isset($this->studentAdvisedSessionAndCourseIds)) {
            $this->studentAdvisedSessionAndCourseIds =
                $this->userRepository->getStudentAdvisedSessionAndCourseIds($this->getId());
        }
        return $this->studentAdvisedSessionAndCourseIds;
    }

    /**
     * @throws Exception
     */
    protected function getLearnerIlmAndOfferingIds(): array
    {
        if (!isset($this->learnerIlmAndOfferingIds)) {
            $this->learnerIlmAndOfferingIds =
                $this->userRepository->getLearnerIlmAndOfferingIds($this->getId());
        }
        return $this->learnerIlmAndOfferingIds;
    }

    protected function getLearnerSessionIds(): array
    {
        if (!isset($this->learnerSessionIds)) {
            $this->learnerSessionIds = $this->userRepository->getLearnerSessionIds($this->getId());
        }
        return $this->learnerSessionIds;
    }

    /**
     * @throws Exception
     */
    protected function getAdministeredCourseAndSchoolIds(): array
    {
        if (!isset($this->administeredCourseAndSchoolIds)) {
            $this->administeredCourseAndSchoolIds = $this->userRepository->getAdministeredCourseAndSchoolIds(
                $this->getId()
            );
        }
        return $this->administeredCourseAndSchoolIds;
    }

    /**
     * @throws Exception
     */
    protected function getDirectedCourseAndSchoolIds(): array
    {
        if (!isset($this->directedCourseAndSchoolIds)) {
            $this->directedCourseAndSchoolIds = $this->userRepository->getDirectedCourseAndSchoolIds($this->getId());
        }
        return $this->directedCourseAndSchoolIds;
    }

    /**
     * @throws Exception
     * @see UserRepository::getInstructedLearnerGroupSchoolIds()
     */
    protected function getInstructedLearnerGroupSchoolIds(): array
    {
        if (!isset($this->instructedLearnerGroupSchoolIds)) {
            $this->instructedLearnerGroupSchoolIds =
                $this->userRepository->getInstructedLearnerGroupSchoolIds($this->getId());
        }
        return $this->instructedLearnerGroupSchoolIds;
    }

    /**
     * @throws Exception
     * @see UserRepository::getInstructorGroupSchoolIds()
     */
    protected function getInstructorGroupSchoolIds(): array
    {
        if (!isset($this->instructorGroupSchoolIds)) {
            $this->instructorGroupSchoolIds =
                $this->userRepository->getInstructorGroupSchoolIds($this->getId());
        }
        return $this->instructorGroupSchoolIds;
    }

    /**
     * @throws Exception
     * @see UserRepository::getLearnerGroupIds()
     */
    protected function getLearnerGroupIds(): array
    {
        if (!isset($this->learnerGroupIds)) {
            $this->learnerGroupIds =
                $this->userRepository->getLearnerGroupIds($this->getId());
        }
        return $this->learnerGroupIds;
    }

    /**
     * @throws Exception
     * @see UserRepository::getInstructorGroupIds()
     */
    protected function getInstructorGroupIds(): array
    {
        if (!isset($this->instructorGroupIds)) {
            $this->instructorGroupIds =
                $this->userRepository->getInstructorGroupIds($this->getId());
        }
        return $this->instructorGroupIds;
    }

    /**
     * As of Symfony 7.3, CRC32 hashed passwords can be stored in the session.
     * Symfony will hash the password of the refreshed user and compare it to the session value.
     * This avoids storing real hashes and allows you to invalidate sessions when a password changes.
     * @link https://symfony.com/blog/new-in-symfony-7-3-security-improvements#support-hashed-passwords-in-the-session
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0" . self::class . "\0password"] = hash('crc32c', $this->password);
        return $data;
    }
}
