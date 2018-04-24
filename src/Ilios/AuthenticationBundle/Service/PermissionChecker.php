<?php

namespace Ilios\AuthenticationBundle\Service;

use Ilios\AuthenticationBundle\Classes\Capabilities;
use Ilios\AuthenticationBundle\Classes\PermissionMatrixInterface;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;

use Ilios\CoreBundle\Entity\CourseInterface;
use Ilios\CoreBundle\Entity\ProgramInterface;
use Ilios\CoreBundle\Entity\ProgramYearInterface;
use Ilios\CoreBundle\Entity\SchoolInterface;
use Ilios\CoreBundle\Entity\SessionInterface;

class PermissionChecker
{
    /**
     * @var PermissionMatrixInterface
     */
    private $matrix;

    public function __construct(PermissionMatrixInterface $matrix)
    {
        $this->matrix = $matrix;
    }

    public function canUpdateCourse(SessionUserInterface $sessionUser, CourseInterface $course): bool
    {
        if ($course->isLocked() || $course->isArchived()) {
            return false;
        }

        $schoolId = $course->getSchool()->getId();

        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_UPDATE_ALL_COURSES);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_UPDATE_ALL_COURSES,
            $rolesInSchool
        )) {
            return true;
        }

        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_UPDATE_THEIR_COURSES);
        $rolesInCourse = $sessionUser->rolesInCourse($course->getId(), $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_UPDATE_THEIR_COURSES,
            $rolesInCourse
        )) {
            return true;
        }

        return false;
    }

    public function canDeleteCourse(SessionUserInterface $sessionUser, CourseInterface $course): bool
    {
        if ($course->isLocked() || $course->isArchived()) {
            return false;
        }

        $schoolId = $course->getSchool()->getId();

        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_DELETE_ALL_COURSES);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_DELETE_ALL_COURSES,
            $rolesInSchool
        )) {
            return true;
        }

        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_DELETE_THEIR_COURSES);
        $rolesInCourse = $sessionUser->rolesInCourse($course->getId(), $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_DELETE_THEIR_COURSES,
            $rolesInCourse
        )) {
            return true;
        }

        return false;
    }

    public function canCreateCourse(SessionUserInterface $sessionUser, SchoolInterface $school): bool
    {
        $schoolId = $school->getId();

        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_CREATE_COURSES);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_CREATE_COURSES,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canUnlockCourse(SessionUserInterface $sessionUser, CourseInterface $course): bool
    {
        $schoolId = $course->getSchool()->getId();

        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_UNLOCK_ALL_COURSES);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_UNLOCK_ALL_COURSES,
            $rolesInSchool
        )) {
            return true;
        }

        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_UNLOCK_THEIR_COURSES);
        $rolesInCourse = $sessionUser->rolesInCourse($course->getId(), $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_UNLOCK_THEIR_COURSES,
            $rolesInCourse
        )) {
            return true;
        }

        return false;
    }

    public function canUpdateSession(SessionUserInterface $sessionUser, SessionInterface $session): bool
    {
        if ($session->getCourse()->isLocked() || $session->getCourse()->isArchived()) {
            return false;
        }

        $schoolId = $session->getSchool()->getId();

        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_UPDATE_ALL_SESSIONS);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_UPDATE_ALL_SESSIONS,
            $rolesInSchool
        )) {
            return true;
        }

        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_UPDATE_THEIR_SESSIONS);
        $rolesInSession = $sessionUser->rolesInSession($session->getId(), $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_UPDATE_THEIR_SESSIONS,
            $rolesInSession
        )) {
            return true;
        }

        return $this->canUpdateCourse($sessionUser, $session->getCourse());
    }

    public function canDeleteSession(SessionUserInterface $sessionUser, SessionInterface $session): bool
    {
        if ($session->getCourse()->isLocked() || $session->getCourse()->isArchived()) {
            return false;
        }

        $schoolId = $session->getSchool()->getId();

        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_DELETE_ALL_SESSIONS);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_DELETE_ALL_SESSIONS,
            $rolesInSchool
        )) {
            return true;
        }

        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_DELETE_THEIR_SESSIONS);
        $rolesInSession = $sessionUser->rolesInSession($session->getId(), $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_DELETE_THEIR_SESSIONS,
            $rolesInSession
        )) {
            return true;
        }

        return $this->canUpdateCourse($sessionUser, $session->getCourse());
    }

    public function canCreateSession(SessionUserInterface $sessionUser, CourseInterface $course): bool
    {
        if ($course->isLocked() || $course->isArchived()) {
            return false;
        }

        $schoolId = $course->getSchool()->getId();

        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_CREATE_SESSIONS);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_CREATE_SESSIONS,
            $rolesInSchool
        )) {
            return true;
        }

        return $this->canUpdateCourse($sessionUser, $course);
    }

    public function canUpdateSessionType(SessionUserInterface $sessionUser, int $schoolId): bool
    {
        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_UPDATE_SESSION_TYPES);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_UPDATE_SESSION_TYPES,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canDeleteSessionType(SessionUserInterface $sessionUser, int $schoolId): bool
    {
        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_DELETE_SESSION_TYPES);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_DELETE_SESSION_TYPES,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canCreateSessionType(SessionUserInterface $sessionUser, int $schoolId): bool
    {
        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_CREATE_SESSION_TYPES);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_CREATE_SESSION_TYPES,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canUpdateDepartment(SessionUserInterface $sessionUser, int $schoolId) : bool
    {
        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_UPDATE_DEPARTMENTS);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_UPDATE_DEPARTMENTS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canDeleteDepartment(SessionUserInterface $sessionUser, int $schoolId) : bool
    {
        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_DELETE_DEPARTMENTS);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_DELETE_DEPARTMENTS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canCreateDepartment(SessionUserInterface $sessionUser, int $schoolId) : bool
    {
        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_CREATE_DEPARTMENTS);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_CREATE_DEPARTMENTS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canUpdateProgram(SessionUserInterface $sessionUser, int $programId, int $schoolId) : bool
    {
        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAMS);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_UPDATE_ALL_PROGRAMS,
            $rolesInSchool
        )) {
            return true;
        }

        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_UPDATE_THEIR_PROGRAMS);
        $rolesInProgram = $sessionUser->rolesInProgram($programId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_UPDATE_THEIR_PROGRAMS,
            $rolesInProgram
        )) {
            return true;
        }

        return false;
    }

    public function canDeleteProgram(SessionUserInterface $sessionUser, int $programId, int $schoolId) : bool
    {
        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_DELETE_ALL_PROGRAMS);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_DELETE_ALL_PROGRAMS,
            $rolesInSchool
        )) {
            return true;
        }

        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_DELETE_THEIR_PROGRAMS);
        $rolesInProgram = $sessionUser->rolesInProgram($programId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_DELETE_THEIR_PROGRAMS,
            $rolesInProgram
        )) {
            return true;
        }

        return false;
    }

    public function canCreateProgram(SessionUserInterface $sessionUser, int $schoolId) : bool
    {
        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_CREATE_PROGRAMS);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_CREATE_PROGRAMS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canUpdateProgramYear(SessionUserInterface $sessionUser, ProgramYearInterface $programYear) : bool
    {
        if ($programYear->isLocked() || $programYear->isArchived()) {
            return false;
        }

        $schoolId = $programYear->getSchool()->getId();

        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_UPDATE_ALL_PROGRAM_YEARS);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_UPDATE_ALL_PROGRAM_YEARS,
            $rolesInSchool
        )) {
            return true;
        }

        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_UPDATE_THEIR_PROGRAM_YEARS);
        $rolesInProgramYear = $sessionUser->rolesInProgramYear($programYear->getId(), $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_UPDATE_THEIR_PROGRAM_YEARS,
            $rolesInProgramYear
        )) {
            return true;
        }

        return $this->canUpdateProgram(
            $sessionUser,
            $programYear->getProgram()->getId(),
            $schoolId
        );
    }

    public function canDeleteProgramYear(SessionUserInterface $sessionUser, ProgramYearInterface $programYear) : bool
    {
        if ($programYear->isLocked() || $programYear->isArchived()) {
            return false;
        }

        $schoolId = $programYear->getSchool()->getId();

        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_DELETE_ALL_PROGRAM_YEARS);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_DELETE_ALL_PROGRAM_YEARS,
            $rolesInSchool
        )) {
            return true;
        }

        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_DELETE_THEIR_PROGRAM_YEARS);
        $rolesInProgramYear = $sessionUser->rolesInProgramYear($programYear->getId(), $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_DELETE_THEIR_PROGRAM_YEARS,
            $rolesInProgramYear
        )) {
            return true;
        }

        return $this->canUpdateProgram(
            $sessionUser,
            $programYear->getProgram()->getId(),
            $schoolId
        );
    }

    public function canCreateProgramYear(SessionUserInterface $sessionUser, ProgramInterface $program): bool
    {
        $schoolId = $program->getSchool()->getId();

        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_CREATE_PROGRAM_YEARS);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_CREATE_PROGRAM_YEARS,
            $rolesInSchool
        )) {
            return true;
        }

        return $this->canUpdateProgram($sessionUser, $program->getId(), $schoolId);
    }

    public function canUnlockProgramYear(SessionUserInterface $sessionUser, ProgramYearInterface $programYear) : bool
    {
        $schoolId = $programYear->getSchool()->getId();

        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_UNLOCK_ALL_PROGRAM_YEARS);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_UNLOCK_ALL_PROGRAM_YEARS,
            $rolesInSchool
        )) {
            return true;
        }

        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_UNLOCK_THEIR_PROGRAM_YEARS);
        $rolesInProgramYear = $sessionUser->rolesInProgramYear($programYear->getId(), $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_UNLOCK_THEIR_PROGRAM_YEARS,
            $rolesInProgramYear
        )) {
            return true;
        }

        return $this->canUpdateProgram(
            $sessionUser,
            $programYear->getProgram()->getId(),
            $schoolId
        );
    }

    public function canUpdateSchoolConfig(SessionUserInterface $sessionUser, int $schoolId) : bool
    {
        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_UPDATE_SCHOOL_CONFIGS);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_UPDATE_SCHOOL_CONFIGS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canDeleteSchoolConfig(SessionUserInterface $sessionUser, int $schoolId) : bool
    {
        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_DELETE_SCHOOL_CONFIGS);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_DELETE_SCHOOL_CONFIGS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canCreateSchoolConfig(SessionUserInterface $sessionUser, int $schoolId) : bool
    {
        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_CREATE_SCHOOL_CONFIGS);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_CREATE_SCHOOL_CONFIGS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canUpdateSchool(SessionUserInterface $sessionUser, int $schoolId) : bool
    {
        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_UPDATE_SCHOOLS);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_UPDATE_SCHOOLS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canUpdateCompetency(SessionUserInterface $sessionUser, int $schoolId) : bool
    {
        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_UPDATE_COMPETENCIES);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_UPDATE_COMPETENCIES,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canDeleteCompetency(SessionUserInterface $sessionUser, int $schoolId) : bool
    {
        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_DELETE_COMPETENCIES);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_DELETE_COMPETENCIES,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canCreateCompetency(SessionUserInterface $sessionUser, int $schoolId) : bool
    {
        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_CREATE_COMPETENCIES);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_CREATE_COMPETENCIES,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canUpdateVocabulary(SessionUserInterface $sessionUser, int $schoolId): bool
    {
        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_UPDATE_VOCABULARIES);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_UPDATE_VOCABULARIES,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canDeleteVocabulary(SessionUserInterface $sessionUser, int $schoolId): bool
    {
        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_DELETE_VOCABULARIES);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_DELETE_VOCABULARIES,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canCreateVocabulary(SessionUserInterface $sessionUser, int $schoolId): bool
    {
        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_CREATE_VOCABULARIES);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_CREATE_VOCABULARIES,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canUpdateTerm(SessionUserInterface $sessionUser, int $schoolId): bool
    {
        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_UPDATE_TERMS);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_UPDATE_TERMS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canDeleteTerm(SessionUserInterface $sessionUser, int $schoolId): bool
    {
        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_DELETE_TERMS);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_DELETE_TERMS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canCreateTerm(SessionUserInterface $sessionUser, int $schoolId): bool
    {
        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_CREATE_TERMS);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_CREATE_TERMS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canUpdateInstructorGroup(SessionUserInterface $sessionUser, int $schoolId): bool
    {
        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_UPDATE_INSTRUCTOR_GROUPS);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_UPDATE_INSTRUCTOR_GROUPS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canDeleteInstructorGroup(SessionUserInterface $sessionUser, int $schoolId): bool
    {
        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_DELETE_INSTRUCTOR_GROUPS);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_DELETE_INSTRUCTOR_GROUPS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canCreateInstructorGroup(SessionUserInterface $sessionUser, int $schoolId): bool
    {
        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_CREATE_INSTRUCTOR_GROUPS);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_CREATE_INSTRUCTOR_GROUPS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canUpdateCurriculumInventoryReport(
        SessionUserInterface $sessionUser,
        int $curriculumInventoryReportId,
        int $schoolId
    ): bool {

        $permittedRoles = $this->matrix->getPermittedRoles(
            $schoolId,
            Capabilities::CAN_UPDATE_ALL_CURRICULUM_INVENTORY_REPORTS
        );
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_UPDATE_ALL_CURRICULUM_INVENTORY_REPORTS,
            $rolesInSchool
        )) {
            return true;
        }

        $permittedRoles = $this->matrix->getPermittedRoles(
            $schoolId,
            Capabilities::CAN_UPDATE_THEIR_CURRICULUM_INVENTORY_REPORTS
        );
        $rolesInCiReport = $sessionUser->rolesInCurriculumInventoryReport(
            $curriculumInventoryReportId,
            $permittedRoles
        );
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_UPDATE_THEIR_CURRICULUM_INVENTORY_REPORTS,
            $rolesInCiReport
        )) {
            return true;
        }

        return false;
    }

    public function canDeleteCurriculumInventoryReport(
        SessionUserInterface $sessionUser,
        int $curriculumInventoryReportId,
        int $schoolId
    ): bool {
        $permittedRoles = $this->matrix->getPermittedRoles(
            $schoolId,
            Capabilities::CAN_DELETE_ALL_CURRICULUM_INVENTORY_REPORTS
        );
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_DELETE_ALL_CURRICULUM_INVENTORY_REPORTS,
            $rolesInSchool
        )) {
            return true;
        }

        $permittedRoles = $this->matrix->getPermittedRoles(
            $schoolId,
            Capabilities::CAN_DELETE_THEIR_CURRICULUM_INVENTORY_REPORTS
        );
        $rolesInCiReport = $sessionUser->rolesInCurriculumInventoryReport(
            $curriculumInventoryReportId,
            $permittedRoles
        );
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_DELETE_THEIR_CURRICULUM_INVENTORY_REPORTS,
            $rolesInCiReport
        )) {
            return true;
        }

        return false;
    }

    public function canCreateCurriculumInventoryReport(
        SessionUserInterface $sessionUser,
        int $schoolId
    ): bool {
        $permittedRoles = $this->matrix->getPermittedRoles(
            $schoolId,
            Capabilities::CAN_CREATE_CURRICULUM_INVENTORY_REPORTS
        );
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_CREATE_CURRICULUM_INVENTORY_REPORTS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    /**
     * Checks if a given user can create CI reports in any of its schools.
     * @param SessionUserInterface $sessionUser
     * @return bool
     */
    public function canCreateCurriculumInventoryReportInAnySchool(SessionUserInterface $sessionUser)
    {
        // limit this to schools that the user performs a non-student function in.
        // the assumption here is that a student will NEVER be able to create other users.
        $schoolIds = $sessionUser->getAssociatedSchoolIdsInNonLearnerFunction();
        $can = false;
        foreach ($schoolIds as $schoolId) {
            if ($this->canCreateCurriculumInventoryReport($sessionUser, $schoolId)) {
                $can = true;
                break;
            }
        }
        return $can;
    }

    public function canUpdateCurriculumInventoryInstitution(SessionUserInterface $sessionUser, int $schoolId) : bool
    {
        $permittedRoles = $this->matrix->getPermittedRoles(
            $schoolId,
            Capabilities::CAN_UPDATE_CURRICULUM_INVENTORY_INSTITUTIONS
        );
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_UPDATE_CURRICULUM_INVENTORY_INSTITUTIONS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canDeleteCurriculumInventoryInstitution(SessionUserInterface $sessionUser, int $schoolId) : bool
    {
        $permittedRoles = $this->matrix->getPermittedRoles(
            $schoolId,
            Capabilities::CAN_DELETE_CURRICULUM_INVENTORY_INSTITUTIONS
        );
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_DELETE_CURRICULUM_INVENTORY_INSTITUTIONS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canCreateCurriculumInventoryInstitution(SessionUserInterface $sessionUser, int $schoolId) : bool
    {
        $permittedRoles = $this->matrix->getPermittedRoles(
            $schoolId,
            Capabilities::CAN_CREATE_CURRICULUM_INVENTORY_INSTITUTIONS
        );
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_CREATE_CURRICULUM_INVENTORY_INSTITUTIONS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canUpdateLearnerGroup(SessionUserInterface $sessionUser, int $schoolId): bool
    {
        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_UPDATE_LEARNER_GROUPS);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_UPDATE_LEARNER_GROUPS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canDeleteLearnerGroup(SessionUserInterface $sessionUser, int $schoolId): bool
    {
        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_DELETE_LEARNER_GROUPS);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_DELETE_LEARNER_GROUPS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canCreateLearnerGroup(SessionUserInterface $sessionUser, int $schoolId): bool
    {
        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_CREATE_LEARNER_GROUPS);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_CREATE_LEARNER_GROUPS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canUpdateUser(SessionUserInterface $sessionUser, int $schoolId): bool
    {
        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_UPDATE_USERS);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_UPDATE_USERS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canDeleteUser(SessionUserInterface $sessionUser, int $schoolId): bool
    {
        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_DELETE_USERS);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_DELETE_USERS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canCreateUser(SessionUserInterface $sessionUser, int $schoolId): bool
    {
        $permittedRoles = $this->matrix->getPermittedRoles($schoolId, Capabilities::CAN_CREATE_USERS);
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId, $permittedRoles);
        if ($this->matrix->hasPermission(
            $schoolId,
            Capabilities::CAN_CREATE_USERS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    /**
     * Checks if a given user can create users in any of its schools.
     * @param SessionUserInterface $sessionUser
     * @return bool
     */
    public function canCreateUsersInAnySchool(SessionUserInterface $sessionUser)
    {
        // limit this to schools that the user performs a non-student function in.
        // the assumption here is that a student will NEVER be able to create other users.
        $schoolIds = $sessionUser->getAssociatedSchoolIdsInNonLearnerFunction();
        $can = false;
        foreach ($schoolIds as $schoolId) {
            if ($this->canCreateUser($sessionUser, $schoolId)) {
                $can = true;
                break;
            }
        }
        return $can;
    }

    /**
     * Checks if a given user can create users in any of its schools.
     * @param SessionUserInterface $sessionUser
     * @return bool
     */
    public function canCreateOrUpdateUsersInAnySchool(SessionUserInterface $sessionUser)
    {
        // limit this to schools that the user performs a non-student function in.
        // the assumption here is that a student will NEVER be able to create other users.
        $schoolIds = $sessionUser->getAssociatedSchoolIdsInNonLearnerFunction();
        $can = false;
        foreach ($schoolIds as $schoolId) {
            if ($this->canCreateUser($sessionUser, $schoolId)) {
                $can = true;
                break;
            }
        }
        foreach ($schoolIds as $schoolId) {
            if ($this->canUpdateUser($sessionUser, $schoolId)) {
                $can = true;
                break;
            }
        }
        return $can;
    }
}
