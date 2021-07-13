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
    public function getAuthentication();

    /**
     * @param string $lastName
     */
    public function setLastName($lastName);

    /**
     * @return string
     */
    public function getLastName();

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName);

    /**
     * @return string
     */
    public function getFirstName();

    /**
     * @param string $middleName
     */
    public function setMiddleName($middleName);

    /**
     * @return string
     */
    public function getMiddleName();

    /**
     * @return string
     */
    public function getFirstAndLastName();

    /**
     * @param string $displayName
     */
    public function setDisplayName($displayName);

    /**
     * @return string
     */
    public function getDisplayName();

    /**
     * @param string $phone
     */
    public function setPhone($phone);

    /**
     * @return string
     */
    public function getPhone();

    /**
     * @param string $email
     */
    public function setEmail($email);

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param string $email
     */
    public function setPreferredEmail($email);

    /**
     * @return string
     */
    public function getPreferredEmail();

    /**
     * @param bool $addedViaIlios
     */
    public function setAddedViaIlios($addedViaIlios);

    /**
     * @return bool
     */
    public function isAddedViaIlios();

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled);

    /**
     * @return bool
     */
    public function isEnabled();

    /**
     * @param string $campusId
     */
    public function setCampusId($campusId);

    /**
     * @return string
     */
    public function getCampusId();

    /**
     * @param string $otherId
     */
    public function setOtherId($otherId);

    /**
     * @return string
     */
    public function getOtherId();

    /**
     * @param bool $examined
     */
    public function setExamined($examined);

    /**
     * @return bool
     */
    public function isExamined();

    /**
     * @param bool $userSyncIgnore
     */
    public function setUserSyncIgnore($userSyncIgnore);

    /**
     * @return bool
     */
    public function isUserSyncIgnore();

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
    public function getIcsFeedKey();

    public function setDirectedCourses(Collection $courses);

    public function addDirectedCourse(CourseInterface $course);

    public function removeDirectedCourse(CourseInterface $course);

    /**
     * @return ArrayCollection|CourseInterface[]
     */
    public function getDirectedCourses();

    public function setAdministeredCourses(Collection $administeredCourses);

    public function addAdministeredCourse(CourseInterface $administeredCourse);

    public function removeAdministeredCourse(CourseInterface $administeredCourse);

    /**
     * @return ArrayCollection|CourseInterface[]
     */
    public function getAdministeredCourses();

    public function setStudentAdvisedCourses(Collection $studentAdvisedCourses);

    public function addStudentAdvisedCourse(CourseInterface $studentAdvisedCourse);

    public function removeStudentAdvisedCourse(CourseInterface $studentAdvisedCourse);

    /**
     * @return ArrayCollection|CourseInterface[]
     */
    public function getStudentAdvisedCourses();

    public function setStudentAdvisedSessions(Collection $studentAdvisedSessions);

    public function addStudentAdvisedSession(SessionInterface $studentAdvisedSession);

    public function removeStudentAdvisedSession(SessionInterface $studentAdvisedSession);

    /**
     * @return ArrayCollection|SessionInterface[]
     */
    public function getStudentAdvisedSessions();

    /**
     * @param int $courseId
     * @return bool
     */
    public function isDirectingCourse($courseId);

    public function setInstructedLearnerGroups(Collection $instructedLearnerGroups);

    public function addInstructedLearnerGroup(LearnerGroupInterface $instructedLearnerGroup);

    public function removeInstructedLearnerGroup(LearnerGroupInterface $instructedLearnerGroup);

    /**
     * @return ArrayCollection|LearnerGroupInterface[]
     */
    public function getInstructedLearnerGroups();

    public function setRoles(Collection $roles);

    public function addRole(UserRoleInterface $role);

    public function removeRole(UserRoleInterface $role);

    /**
     * @return ArrayCollection|UserRoleInterface[]
     */
    public function getRoles();

    public function setReports(Collection $reports);

    public function addReport(ReportInterface $report);

    public function removeReport(ReportInterface $report);

    /**
     * @return ArrayCollection|ReportInterface[]
     */
    public function getReports();

    public function setPendingUserUpdates(Collection $pendingUserUpdates);

    public function addPendingUserUpdate(PendingUserUpdateInterface $pendingUserUpdate);

    public function removePendingUserUpdate(PendingUserUpdateInterface $pendingUserUpdate);

    /**
     * @return ArrayCollection|PendingUserUpdateInterface[]
     */
    public function getPendingUserUpdates();

    /**
     * @return ArrayCollection[School]
     */
    public function getAllSchools();

    public function setAuditLogs(Collection $auditLogs);

    public function addAuditLog(AuditLogInterface $auditLog);

    public function removeAuditLog(AuditLogInterface $auditLog);

    /**
     * @return ArrayCollection|AuditLogInterface[]
     */
    public function getAuditLogs();

    public function setAlerts(Collection $alerts);

    public function addAlert(AlertInterface $alert);

    public function removeAlert(AlertInterface $alert);

    /**
     * @return ArrayCollection|AlertInterface[]
     */
    public function getAlerts();

    public function setAdministeredSessions(Collection $administeredSessions);

    public function addAdministeredSession(SessionInterface $administeredSession);

    public function removeAdministeredSession(SessionInterface $administeredSession);

    /**
     * @return ArrayCollection|SessionInterface[]
     */
    public function getAdministeredSessions();

    public function setLearnerIlmSessions(Collection $learnerIlmSessions);

    public function addLearnerIlmSession(IlmSessionInterface $learnerIlmSession);

    public function removeLearnerIlmSession(IlmSessionInterface $learnerIlmSession);

    /**
     * @return ArrayCollection|IlmSessionInterface[]
     */
    public function getLearnerIlmSessions();

    public function setDirectedSchools(Collection $schools);

    public function addDirectedSchool(SchoolInterface $school);

    public function removeDirectedSchool(SchoolInterface $school);

    /**
     * @return ArrayCollection|SchoolInterface[]
     */
    public function getDirectedSchools();

    public function setAdministeredSchools(Collection $administeredSchools);

    public function addAdministeredSchool(SchoolInterface $administeredSchool);

    public function removeAdministeredSchool(SchoolInterface $administeredSchool);

    /**
     * @return ArrayCollection|SchoolInterface[]
     */
    public function getAdministeredSchools();

    public function setDirectedPrograms(Collection $programs);

    public function addDirectedProgram(ProgramInterface $program);

    public function removeDirectedProgram(ProgramInterface $program);

    /**
     * @return ArrayCollection|ProgramInterface[]
     */
    public function getDirectedPrograms();

    /**
     * @param bool $root
     */
    public function setRoot($root);

    /**
     * @return bool
     */
    public function isRoot();

    /**
     * @return ArrayCollection|CurriculumInventoryReportInterface[]
     */
    public function getAdministeredCurriculumInventoryReports();

    public function setAdministeredCurriculumInventoryReports(Collection $reports);

    public function addAdministeredCurriculumInventoryReport(CurriculumInventoryReportInterface $report);

    public function removeAdministeredCurriculumInventoryReport(CurriculumInventoryReportInterface $report);
}
