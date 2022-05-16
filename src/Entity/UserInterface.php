<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use App\Traits\AlertableEntityInterface;
use App\Traits\CohortsEntityInterface;
use App\Traits\InstructorGroupsEntityInterface;
use App\Traits\LearnerGroupsEntityInterface;
use App\Traits\LearningMaterialsEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\StringableEntityToIdInterface;
use App\Traits\OfferingsEntityInterface;
use App\Traits\ProgramYearsEntityInterface;
use App\Traits\SchoolEntityInterface;

interface UserInterface extends
    IdentifiableEntityInterface,
    StringableEntityToIdInterface,
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
    public function setAuthentication(AuthenticationInterface $authentication = null);
    public function getAuthentication(): ?AuthenticationInterface;

    public function setLastName(string $lastName);
    public function getLastName(): string;

    public function setFirstName(string $firstName);
    public function getFirstName(): string;

    public function setMiddleName(?string $middleName);
    public function getMiddleName(): ?string;

    public function getFirstAndLastName(): string;

    public function setDisplayName(?string $displayName);
    public function getDisplayName(): ?string;

    public function setPronouns(?string $pronouns);
    public function getPronouns(): ?string;

    public function setPhone(?string $phone);
    public function getPhone(): ?string;

    public function setEmail(string $email);
    public function getEmail(): string;

    public function setPreferredEmail(?string $email);
    public function getPreferredEmail(): ?string;

    public function setAddedViaIlios(bool $addedViaIlios);
    public function isAddedViaIlios(): bool;

    public function setEnabled(bool $enabled);
    public function isEnabled(): bool;

    public function setCampusId(?string $campusId);
    public function getCampusId(): ?string;

    public function setOtherId(?string $otherId);
    public function getOtherId(): ?string;

    public function setExamined(bool $examined);
    public function isExamined(): bool;

    public function setUserSyncIgnore(bool $userSyncIgnore);
    public function isUserSyncIgnore(): bool;

    /**
     * Generate a random string to use as the calendar feed url
     */
    public function generateIcsFeedKey();

    public function setIcsFeedKey(string $icsFeedKey);
    public function getIcsFeedKey(): string;

    public function setDirectedCourses(Collection $courses);
    public function addDirectedCourse(CourseInterface $course);
    public function removeDirectedCourse(CourseInterface $course);
    public function getDirectedCourses(): Collection;

    public function setAdministeredCourses(Collection $administeredCourses);
    public function addAdministeredCourse(CourseInterface $administeredCourse);
    public function removeAdministeredCourse(CourseInterface $administeredCourse);
    public function getAdministeredCourses(): Collection;

    public function setStudentAdvisedCourses(Collection $studentAdvisedCourses);
    public function addStudentAdvisedCourse(CourseInterface $studentAdvisedCourse);
    public function removeStudentAdvisedCourse(CourseInterface $studentAdvisedCourse);
    public function getStudentAdvisedCourses(): Collection;

    public function setStudentAdvisedSessions(Collection $studentAdvisedSessions);
    public function addStudentAdvisedSession(SessionInterface $studentAdvisedSession);
    public function removeStudentAdvisedSession(SessionInterface $studentAdvisedSession);
    public function getStudentAdvisedSessions(): Collection;

    public function isDirectingCourse(int $courseId): bool;

    public function setInstructedLearnerGroups(Collection $instructedLearnerGroups);
    public function addInstructedLearnerGroup(LearnerGroupInterface $instructedLearnerGroup);
    public function removeInstructedLearnerGroup(LearnerGroupInterface $instructedLearnerGroup);
    public function getInstructedLearnerGroups(): Collection;

    public function setRoles(Collection $roles);
    public function addRole(UserRoleInterface $role);
    public function removeRole(UserRoleInterface $role);
    public function getRoles(): Collection;

    public function setReports(Collection $reports);
    public function addReport(ReportInterface $report);
    public function removeReport(ReportInterface $report);
    public function getReports(): Collection;

    public function setPendingUserUpdates(Collection $pendingUserUpdates);
    public function addPendingUserUpdate(PendingUserUpdateInterface $pendingUserUpdate);
    public function removePendingUserUpdate(PendingUserUpdateInterface $pendingUserUpdate);
    public function getPendingUserUpdates(): Collection;

    public function getAllSchools(): Collection;

    public function setAuditLogs(Collection $auditLogs);
    public function addAuditLog(AuditLogInterface $auditLog);
    public function removeAuditLog(AuditLogInterface $auditLog);
    public function getAuditLogs(): Collection;

    public function setAlerts(Collection $alerts);
    public function addAlert(AlertInterface $alert);
    public function removeAlert(AlertInterface $alert);
    public function getAlerts(): Collection;

    public function setAdministeredSessions(Collection $administeredSessions);
    public function addAdministeredSession(SessionInterface $administeredSession);
    public function removeAdministeredSession(SessionInterface $administeredSession);
    public function getAdministeredSessions(): Collection;

    public function setLearnerIlmSessions(Collection $learnerIlmSessions);
    public function addLearnerIlmSession(IlmSessionInterface $learnerIlmSession);
    public function removeLearnerIlmSession(IlmSessionInterface $learnerIlmSession);
    public function getLearnerIlmSessions(): Collection;

    public function setDirectedSchools(Collection $schools);
    public function addDirectedSchool(SchoolInterface $school);
    public function removeDirectedSchool(SchoolInterface $school);
    public function getDirectedSchools(): Collection;

    public function setAdministeredSchools(Collection $administeredSchools);
    public function addAdministeredSchool(SchoolInterface $administeredSchool);
    public function removeAdministeredSchool(SchoolInterface $administeredSchool);
    public function getAdministeredSchools(): Collection;

    public function setDirectedPrograms(Collection $programs);
    public function addDirectedProgram(ProgramInterface $program);
    public function removeDirectedProgram(ProgramInterface $program);
    public function getDirectedPrograms(): Collection;

    public function setRoot(bool $root);
    public function isRoot(): bool;

    public function getAdministeredCurriculumInventoryReports(): Collection;
    public function setAdministeredCurriculumInventoryReports(Collection $reports);
    public function addAdministeredCurriculumInventoryReport(CurriculumInventoryReportInterface $report);
    public function removeAdministeredCurriculumInventoryReport(CurriculumInventoryReportInterface $report);

    public function setPrimaryCohort(CohortInterface $primaryCohort = null);
    public function getPrimaryCohort(): ?CohortInterface;
}
