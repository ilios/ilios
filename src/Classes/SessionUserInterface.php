<?php

declare(strict_types=1);

namespace App\Classes;

use App\Entity\SchoolInterface;
use App\Entity\UserInterface as IliosUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use DateTime;

interface SessionUserInterface extends UserInterface, EquatableInterface
{
    /**
     * Is this user a root user
     *
     * @return bool
     */
    public function isRoot();

    /**
     * Is this user enabled
     *
     * @return bool
     */
    public function isEnabled();

    /**
     * When is the last moment that a users token would be valid
     *
     * @return DateTime | null
     */
    public function tokenNotValidBefore();

    /**
     * Get user's id
     *
     * @return int
     */
    public function getId();

    /**
     * Get user's primary school id
     *
     * @return int
     */
    public function getSchoolId();

    /**
     * Get the ids of all schools that this user is associated with
     * in a non learner function (e.g. as session administrator, program director, etc.)
     *
     * @return array
     */
    public function getAssociatedSchoolIdsInNonLearnerFunction();

    /**
     * Check if the passed user is our session user by id
     *
     * @return bool
     */
    public function isTheUser(IliosUserInterface $user);

    /**
     * Check if the passed school is our user's primary school by id
     *
     * @return bool
     */
    public function isThePrimarySchool(SchoolInterface $school);

    /**
     * Check if a user is a director of a course
     * @return bool
     */
    public function isDirectingCourse(int $courseId);
    public function isAdministeringCourse(int $courseId): bool;

    public function isDirectingSchool(int $schoolId): bool;
    public function isAdministeringSchool(int $schoolId): bool;
    public function isDirectingCourseInSchool(int $schoolId): bool;
    public function isAdministeringCourseInSchool(int $schoolId): bool;
    public function isAdministeringSessionInSchool(int $schoolId): bool;
    public function isTeachingCourseInSchool(int $schoolId): bool;
    public function isTeachingCourse(int $courseId): bool;
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
    ): array;
    public function rolesInCourse(
        int $courseId,
        $roles = [
            UserRoles::COURSE_DIRECTOR,
            UserRoles::COURSE_ADMINISTRATOR,
            UserRoles::SESSION_ADMINISTRATOR,
            UserRoles::COURSE_INSTRUCTOR
        ]
    ): array;
    public function isAdministeringSessionInCourse(int $courseId): bool;
    public function isAdministeringSession(int $sessionId): bool;
    public function isTeachingSession(int $sessionId): bool;
    public function isInstructingOffering(int $sessionId): bool;
    public function isInstructingIlm(int $sessionId): bool;
    public function isStudentAdvisorInSession(int $sessionId): bool;
    public function isStudentAdvisorInCourse(int $courseId): bool;
    public function isLearnerInOffering(int $offeringId): bool;
    public function isLearnerInIlm(int $ilmId): bool;

    public function rolesInSession(
        int $sessionId,
        $roles = [UserRoles::SESSION_ADMINISTRATOR, UserRoles::SESSION_INSTRUCTOR]
    ): array;
    public function rolesInProgram(
        int $programId,
        $roles = [UserRoles::PROGRAM_DIRECTOR, UserRoles::PROGRAM_YEAR_DIRECTOR]
    ): array;
    public function isDirectingProgram(int $programId): bool;
    public function isDirectingProgramInSchool(int $schoolId): bool;
    public function rolesInProgramYear(int $programYearId, $roles = [UserRoles::PROGRAM_YEAR_DIRECTOR]): array;
    public function isDirectingProgramYear(int $programYearId): bool;
    public function isDirectingProgramYearInProgram(int $programId): bool;
    public function isAdministeringCurriculumInventoryReportInSchool(int $schoolId): bool;
    public function isAdministeringCurriculumInventoryReport(int $curriculumInventoryReportId): bool;
    public function rolesInCurriculumInventoryReport(
        int $curriculumInventoryReportId,
        $roles = [UserRoles::CURRICULUM_INVENTORY_REPORT_ADMINISTRATOR]
    ): array;

    /**
     * Checks if this user is performing any non-student function in the system,
     * such as teaching, directing or administering courses, programs, etc.
     */
    public function performsNonLearnerFunction(): bool;

    public function getDirectedCourseIds(): array;

    public function getAdministeredCourseIds(): array;

    public function getDirectedSchoolIds(): array;

    public function getAdministeredSchoolIds(): array;

    public function getDirectedCourseSchoolIds(): array;

    public function getAdministeredCourseSchoolIds(): array;

    public function getAdministeredSessionSchoolIds(): array;

    public function getAdministeredSessionCourseIds(): array;


    public function getTaughtCourseIds(): array;

    public function getAdministeredSessionIds(): array;

    public function getInstructedSessionIds(): array;

    public function getInstructedIlmIds(): array;

    public function getInstructedOfferingIds(): array;

    public function getTaughtCourseSchoolIds(): array;

    public function getDirectedProgramIds(): array;

    public function getDirectedProgramSchoolIds(): array;

    /**
     * @@return array
     */
    public function getDirectedProgramYearIds(): array;

    public function getDirectedProgramYearProgramIds(): array;

    public function getAdministeredCurriculumInventoryReportIds(): array;

    public function getAdministeredCurriculumInventoryReportSchoolIds(): array;

    public function isInLearnerGroup(int $learnerGroupId): bool;

    public function getCourseIdsLinkedToProgramsDirectedByUser(): array;

    public function isDirectingProgramLinkedToCourse(int $courseId): bool;
}
