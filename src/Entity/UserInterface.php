<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Traits\AlertableEntityInterface;
use App\Traits\CohortsEntityInterface;
use App\Traits\InstructorGroupsEntityInterface;
use App\Traits\LearnerGroupsEntityInterface;
use App\Traits\LearningMaterialsEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\StringableEntityInterface;
use App\Traits\OfferingsEntityInterface;
use App\Traits\ProgramYearsEntityInterface;
use App\Traits\SchoolEntityInterface;

/**
 * Interface UserInterface
 */
interface UserInterface extends
    IdentifiableEntityInterface,
    StringableEntityInterface,
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
    /**
     * @param AuthenticationInterface|null $authentication
     */
    public function setAuthentication(AuthenticationInterface $authentication = null);


    /**
     * @return AuthenticationInterface
     */
    public function getAuthentication(): AuthenticationInterface;

    /**
     * @param string $lastName
     */
    public function setLastName($lastName);

    /**
     * @return string
     */
    public function getLastName(): string;

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName);

    /**
     * @return string
     */
    public function getFirstName(): string;

    /**
     * @param string $middleName
     */
    public function setMiddleName($middleName);

    /**
     * @return string
     */
    public function getMiddleName(): string;

    /**
     * @return string
     */
    public function getFirstAndLastName(): string;

    /**
     * @param string $displayName
     */
    public function setDisplayName($displayName);

    /**
     * @return string
     */
    public function getDisplayName(): string;

    /**
     * @param string $phone
     */
    public function setPhone($phone);

    /**
     * @return string
     */
    public function getPhone(): string;

    /**
     * @param string $email
     */
    public function setEmail($email);

    /**
     * @return string
     */
    public function getEmail(): string;

    /**
     * @param string $email
     */
    public function setPreferredEmail($email);

    /**
     * @return string
     */
    public function getPreferredEmail(): string;

    /**
     * @param bool $addedViaIlios
     */
    public function setAddedViaIlios($addedViaIlios);

    /**
     * @return bool
     */
    public function isAddedViaIlios(): bool;

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled);

    /**
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * @param string $campusId
     */
    public function setCampusId($campusId);

    /**
     * @return string
     */
    public function getCampusId(): string;

    /**
     * @param string $otherId
     */
    public function setOtherId($otherId);

    /**
     * @return string
     */
    public function getOtherId(): string;

    /**
     * @param bool $examined
     */
    public function setExamined($examined);

    /**
     * @return bool
     */
    public function isExamined(): bool;

    /**
     * @param bool $userSyncIgnore
     */
    public function setUserSyncIgnore($userSyncIgnore);

    /**
     * @return bool
     */
    public function isUserSyncIgnore(): bool;

    /**
     * Generate a random string to use as the calendar feed url
     */
    public function generateIcsFeedKey();

    /**
     * Sets the ICS Feed Key
     * @param string $icsFeedKey
     */
    public function setIcsFeedKey($icsFeedKey);

    /**
     * @return string
     */
    public function getIcsFeedKey(): string;

    public function setDirectedCourses(Collection $courses);

    public function addDirectedCourse(CourseInterface $course);

    public function removeDirectedCourse(CourseInterface $course);

    /**
     * @return ArrayCollection|CourseInterface[]
     */
    public function getDirectedCourses(): Collection;

    public function setAdministeredCourses(Collection $administeredCourses);

    public function addAdministeredCourse(CourseInterface $administeredCourse);

    public function removeAdministeredCourse(CourseInterface $administeredCourse);

    /**
     * @return ArrayCollection|CourseInterface[]
     */
    public function getAdministeredCourses(): Collection;

    public function setStudentAdvisedCourses(Collection $studentAdvisedCourses);

    public function addStudentAdvisedCourse(CourseInterface $studentAdvisedCourse);

    public function removeStudentAdvisedCourse(CourseInterface $studentAdvisedCourse);

    /**
     * @return ArrayCollection|CourseInterface[]
     */
    public function getStudentAdvisedCourses(): Collection;

    public function setStudentAdvisedSessions(Collection $studentAdvisedSessions);

    public function addStudentAdvisedSession(SessionInterface $studentAdvisedSession);

    public function removeStudentAdvisedSession(SessionInterface $studentAdvisedSession);

    /**
     * @return ArrayCollection|SessionInterface[]
     */
    public function getStudentAdvisedSessions(): Collection;

    /**
     * @param int $courseId
     * @return bool
     */
    public function isDirectingCourse($courseId): bool;

    public function setInstructedLearnerGroups(Collection $instructedLearnerGroups);

    public function addInstructedLearnerGroup(LearnerGroupInterface $instructedLearnerGroup);

    public function removeInstructedLearnerGroup(LearnerGroupInterface $instructedLearnerGroup);

    /**
     * @return ArrayCollection|LearnerGroupInterface[]
     */
    public function getInstructedLearnerGroups(): Collection;

    public function setRoles(Collection $roles);

    public function addRole(UserRoleInterface $role);

    public function removeRole(UserRoleInterface $role);

    /**
     * @return ArrayCollection|UserRoleInterface[]
     */
    public function getRoles(): Collection;

    public function setReports(Collection $reports);

    public function addReport(ReportInterface $report);

    public function removeReport(ReportInterface $report);

    /**
     * @return ArrayCollection|ReportInterface[]
     */
    public function getReports(): Collection;

    public function setPendingUserUpdates(Collection $pendingUserUpdates);

    public function addPendingUserUpdate(PendingUserUpdateInterface $pendingUserUpdate);

    public function removePendingUserUpdate(PendingUserUpdateInterface $pendingUserUpdate);

    /**
     * @return ArrayCollection|PendingUserUpdateInterface[]
     */
    public function getPendingUserUpdates(): Collection;

    /**
     * @return ArrayCollection[School]
     */
    public function getAllSchools(): Collection;

    public function setAuditLogs(Collection $auditLogs);

    public function addAuditLog(AuditLogInterface $auditLog);

    public function removeAuditLog(AuditLogInterface $auditLog);

    /**
     * @return ArrayCollection|AuditLogInterface[]
     */
    public function getAuditLogs(): Collection;

    public function setAlerts(Collection $alerts);

    public function addAlert(AlertInterface $alert);

    public function removeAlert(AlertInterface $alert);

    /**
     * @return ArrayCollection|AlertInterface[]
     */
    public function getAlerts(): Collection;

    public function setAdministeredSessions(Collection $administeredSessions);

    public function addAdministeredSession(SessionInterface $administeredSession);

    public function removeAdministeredSession(SessionInterface $administeredSession);

    /**
     * @return ArrayCollection|SessionInterface[]
     */
    public function getAdministeredSessions(): Collection;

    public function setLearnerIlmSessions(Collection $learnerIlmSessions);

    public function addLearnerIlmSession(IlmSessionInterface $learnerIlmSession);

    public function removeLearnerIlmSession(IlmSessionInterface $learnerIlmSession);

    /**
     * @return ArrayCollection|IlmSessionInterface[]
     */
    public function getLearnerIlmSessions(): Collection;

    public function setDirectedSchools(Collection $schools);

    public function addDirectedSchool(SchoolInterface $school);

    public function removeDirectedSchool(SchoolInterface $school);

    /**
     * @return ArrayCollection|SchoolInterface[]
     */
    public function getDirectedSchools(): Collection;

    public function setAdministeredSchools(Collection $administeredSchools);

    public function addAdministeredSchool(SchoolInterface $administeredSchool);

    public function removeAdministeredSchool(SchoolInterface $administeredSchool);

    /**
     * @return ArrayCollection|SchoolInterface[]
     */
    public function getAdministeredSchools(): Collection;

    public function setDirectedPrograms(Collection $programs);

    public function addDirectedProgram(ProgramInterface $program);

    public function removeDirectedProgram(ProgramInterface $program);

    /**
     * @return ArrayCollection|ProgramInterface[]
     */
    public function getDirectedPrograms(): Collection;

    /**
     * @param bool $root
     */
    public function setRoot($root);

    /**
     * @return bool
     */
    public function isRoot(): bool;

    /**
     * @return ArrayCollection|CurriculumInventoryReportInterface[]
     */
    public function getAdministeredCurriculumInventoryReports(): Collection;

    public function setAdministeredCurriculumInventoryReports(Collection $reports);

    public function addAdministeredCurriculumInventoryReport(CurriculumInventoryReportInterface $report);

    public function removeAdministeredCurriculumInventoryReport(CurriculumInventoryReportInterface $report);
}
