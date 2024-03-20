<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\EnableableEntityInterface;
use Doctrine\Common\Collections\Collection;
use App\Traits\AlertableEntityInterface;
use App\Traits\CohortsEntityInterface;
use App\Traits\InstructorGroupsEntityInterface;
use App\Traits\LearnerGroupsEntityInterface;
use App\Traits\LearningMaterialsEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\OfferingsEntityInterface;
use App\Traits\ProgramYearsEntityInterface;
use App\Traits\SchoolEntityInterface;

interface UserInterface extends
    EnableableEntityInterface,
    IdentifiableEntityInterface,
    OfferingsEntityInterface,
    ProgramYearsEntityInterface,
    LoggableEntityInterface,
    SchoolEntityInterface,
    AlertableEntityInterface,
    LearnerGroupsEntityInterface,
    CohortsEntityInterface,
    InstructorGroupsEntityInterface,
    LearningMaterialsEntityInterface
{
    public function setAuthentication(?AuthenticationInterface $authentication = null): void;
    public function getAuthentication(): ?AuthenticationInterface;

    public function setLastName(string $lastName): void;
    public function getLastName(): string;

    public function setFirstName(string $firstName): void;
    public function getFirstName(): string;

    public function setMiddleName(?string $middleName): void;
    public function getMiddleName(): ?string;

    public function getFirstAndLastName(): string;

    public function setDisplayName(?string $displayName): void;
    public function getDisplayName(): ?string;

    public function setPronouns(?string $pronouns): void;
    public function getPronouns(): ?string;

    public function setPhone(?string $phone): void;
    public function getPhone(): ?string;

    public function setEmail(string $email): void;
    public function getEmail(): string;

    public function setPreferredEmail(?string $email): void;
    public function getPreferredEmail(): ?string;

    public function setAddedViaIlios(bool $addedViaIlios): void;
    public function isAddedViaIlios(): bool;

    public function setCampusId(?string $campusId): void;
    public function getCampusId(): ?string;

    public function setOtherId(?string $otherId): void;
    public function getOtherId(): ?string;

    public function setExamined(bool $examined): void;
    public function isExamined(): bool;

    public function setUserSyncIgnore(bool $userSyncIgnore): void;
    public function isUserSyncIgnore(): bool;

    /**
     * Generate a random string to use as the calendar feed url
     */
    public function generateIcsFeedKey(): void;

    public function setIcsFeedKey(string $icsFeedKey): void;
    public function getIcsFeedKey(): string;

    public function setDirectedCourses(Collection $courses): void;
    public function addDirectedCourse(CourseInterface $course): void;
    public function removeDirectedCourse(CourseInterface $course): void;
    public function getDirectedCourses(): Collection;

    public function setAdministeredCourses(Collection $courses): void;
    public function addAdministeredCourse(CourseInterface $course): void;
    public function removeAdministeredCourse(CourseInterface $course): void;
    public function getAdministeredCourses(): Collection;

    public function setStudentAdvisedCourses(Collection $courses): void;
    public function addStudentAdvisedCourse(CourseInterface $course): void;
    public function removeStudentAdvisedCourse(CourseInterface $course): void;
    public function getStudentAdvisedCourses(): Collection;

    public function setStudentAdvisedSessions(Collection $sessions): void;
    public function addStudentAdvisedSession(SessionInterface $session): void;
    public function removeStudentAdvisedSession(SessionInterface $session): void;
    public function getStudentAdvisedSessions(): Collection;

    public function isDirectingCourse(int $courseId): bool;

    public function setInstructedLearnerGroups(Collection $learnerGroups): void;
    public function addInstructedLearnerGroup(LearnerGroupInterface $learnerGroup): void;
    public function removeInstructedLearnerGroup(LearnerGroupInterface $learnerGroup): void;
    public function getInstructedLearnerGroups(): Collection;

    public function setRoles(Collection $roles): void;
    public function addRole(UserRoleInterface $role): void;
    public function removeRole(UserRoleInterface $role): void;
    public function getRoles(): Collection;

    public function setReports(Collection $reports): void;
    public function addReport(ReportInterface $report): void;
    public function removeReport(ReportInterface $report): void;
    public function getReports(): Collection;

    public function setPendingUserUpdates(Collection $pendingUserUpdates): void;
    public function addPendingUserUpdate(PendingUserUpdateInterface $pendingUserUpdate): void;
    public function removePendingUserUpdate(PendingUserUpdateInterface $pendingUserUpdate): void;
    public function getPendingUserUpdates(): Collection;

    public function getAllSchools(): Collection;

    public function setAuditLogs(Collection $auditLogs): void;
    public function addAuditLog(AuditLogInterface $auditLog): void;
    public function removeAuditLog(AuditLogInterface $auditLog): void;
    public function getAuditLogs(): Collection;

    public function setAdministeredSessions(Collection $sessions): void;
    public function addAdministeredSession(SessionInterface $session): void;
    public function removeAdministeredSession(SessionInterface $session): void;
    public function getAdministeredSessions(): Collection;

    public function setLearnerIlmSessions(Collection $ilmSessions): void;
    public function addLearnerIlmSession(IlmSessionInterface $ilmSession): void;
    public function removeLearnerIlmSession(IlmSessionInterface $ilmSession): void;
    public function getLearnerIlmSessions(): Collection;

    public function setDirectedSchools(Collection $schools): void;
    public function addDirectedSchool(SchoolInterface $school): void;
    public function removeDirectedSchool(SchoolInterface $school): void;
    public function getDirectedSchools(): Collection;

    public function setAdministeredSchools(Collection $schools): void;
    public function addAdministeredSchool(SchoolInterface $school): void;
    public function removeAdministeredSchool(SchoolInterface $school): void;
    public function getAdministeredSchools(): Collection;

    public function setDirectedPrograms(Collection $programs): void;
    public function addDirectedProgram(ProgramInterface $program): void;
    public function removeDirectedProgram(ProgramInterface $program): void;
    public function getDirectedPrograms(): Collection;

    public function setRoot(bool $root): void;
    public function isRoot(): bool;

    public function getAdministeredCurriculumInventoryReports(): Collection;
    public function setAdministeredCurriculumInventoryReports(Collection $reports): void;
    public function addAdministeredCurriculumInventoryReport(CurriculumInventoryReportInterface $report): void;
    public function removeAdministeredCurriculumInventoryReport(CurriculumInventoryReportInterface $report): void;

    public function setPrimaryCohort(?CohortInterface $primaryCohort = null): void;
    public function getPrimaryCohort(): ?CohortInterface;

    public function setSessionMaterialStatuses(Collection $sessionMaterialStatuses): void;
    public function addSessionMaterialStatus(UserSessionMaterialStatus $sessionMaterialStatus): void;
    public function removeSessionMaterialStatus(UserSessionMaterialStatus $sessionMaterialStatus): void;
    public function getSessionMaterialStatuses(): Collection;

    public function setInstructorIlmSessions(Collection $sessions): void;
    public function addInstructorIlmSession(IlmSessionInterface $session): void;
    public function removeInstructorIlmSession(IlmSessionInterface $session): void;
    public function getInstructorIlmSessions(): Collection;

    public function setInstructedOfferings(Collection $instructedOfferings): void;
    public function addInstructedOffering(Offering $instructedOffering): void;
    public function removeInstructedOffering(Offering $instructedOffering): void;
    public function getInstructedOfferings(): Collection;
}
