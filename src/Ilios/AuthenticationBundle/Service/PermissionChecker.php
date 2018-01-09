<?php

namespace Ilios\AuthenticationBundle\Service;

use Ilios\AuthenticationBundle\Classes\Capabilities;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\AuthenticationBundle\Classes\UserRoles;

use Ilios\CoreBundle\Entity\CohortInterface;
use Ilios\CoreBundle\Entity\CourseInterface;
use Ilios\CoreBundle\Entity\DTO\SchoolDTO;
use Ilios\CoreBundle\Entity\Manager\SchoolManager;
use Ilios\CoreBundle\Entity\ProgramInterface;
use Ilios\CoreBundle\Entity\ProgramYearInterface;
use Ilios\CoreBundle\Entity\SchoolInterface;
use Ilios\CoreBundle\Entity\SessionInterface;

class PermissionChecker
{
    /**
     * @var SchoolManager
     */
    private $schoolManager;

    /**
     * @var array
     */
    private $matrix;

    public function __construct(SchoolManager $schoolManager)
    {
        $this->schoolManager = $schoolManager;
        $schoolDtos = $this->schoolManager->findDTOsBy([]);
        $this->matrix = [];
        /** @var SchoolDTO $schoolDto */
        foreach ($schoolDtos as $schoolDto) {
            $arr = [];
            $allRoles = [
                UserRoles::COURSE_ADMINISTRATOR,
                UserRoles::COURSE_DIRECTOR,
                UserRoles::COURSE_INSTRUCTOR,
                UserRoles::SCHOOL_ADMINISTRATOR,
                UserRoles::SCHOOL_DIRECTOR,
                UserRoles::PROGRAM_DIRECTOR,
                UserRoles::PROGRAM_YEAR_DIRECTOR,
                UserRoles::SESSION_ADMINISTRATOR,
                UserRoles::SESSION_INSTRUCTOR,
            ];
            $arr[Capabilities::CAN_UPDATE_ALL_COURSES] = $allRoles;
            $arr[Capabilities::CAN_CREATE_COURSES] = $allRoles;
            $arr[Capabilities::CAN_DELETE_ALL_COURSES] = $allRoles;
            $arr[Capabilities::CAN_UNLOCK_ALL_COURSES] = $allRoles;
            $arr[Capabilities::CAN_UNARCHIVE_ALL_COURSES] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_THEIR_COURSES] = $allRoles;
            $arr[Capabilities::CAN_DELETE_THEIR_COURSES] = $allRoles;
            $arr[Capabilities::CAN_UNLOCK_THEIR_COURSES] = $allRoles;
            $arr[Capabilities::CAN_UNARCHIVE_THEIR_COURSES] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_ALL_SESSIONS] = $allRoles;
            $arr[Capabilities::CAN_CREATE_SESSIONS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_ALL_SESSIONS] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_THEIR_SESSIONS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_THEIR_SESSIONS] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_SESSION_TYPES] = $allRoles;
            $arr[Capabilities::CAN_CREATE_SESSION_TYPES] = $allRoles;
            $arr[Capabilities::CAN_DELETE_SESSION_TYPES] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_DEPARTMENTS] = $allRoles;
            $arr[Capabilities::CAN_CREATE_DEPARTMENTS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_DEPARTMENTS] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_SCHOOL_CONFIGS] = $allRoles;
            $arr[Capabilities::CAN_CREATE_SCHOOL_CONFIGS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_SCHOOL_CONFIGS] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_ALL_PROGRAMS] = $allRoles;
            $arr[Capabilities::CAN_CREATE_PROGRAMS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_ALL_PROGRAMS] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_THEIR_PROGRAMS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_THEIR_PROGRAMS] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_ALL_PROGRAM_YEARS] = $allRoles;
            $arr[Capabilities::CAN_CREATE_PROGRAM_YEARS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_ALL_PROGRAM_YEARS] = $allRoles;
            $arr[Capabilities::CAN_UNLOCK_ALL_PROGRAM_YEARS] = $allRoles;
            $arr[Capabilities::CAN_UNARCHIVE_ALL_PROGRAM_YEARS] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_THEIR_PROGRAM_YEARS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_THEIR_PROGRAM_YEARS] = $allRoles;
            $arr[Capabilities::CAN_UNLOCK_THEIR_PROGRAM_YEARS] = $allRoles;
            $arr[Capabilities::CAN_UNARCHIVE_THEIR_PROGRAM_YEARS] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_ALL_COHORTS] = $allRoles;
            $arr[Capabilities::CAN_CREATE_COHORTS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_ALL_COHORTS] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_THEIR_COHORTS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_THEIR_COHORTS] = $allRoles;
            ;
            $arr[Capabilities::CAN_UPDATE_SCHOOL_CONFIGS] = $allRoles;
            $arr[Capabilities::CAN_CREATE_SCHOOL_CONFIGS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_SCHOOL_CONFIGS] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_SCHOOLS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_SCHOOLS] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_COMPETENCIES] = $allRoles;
            $arr[Capabilities::CAN_CREATE_COMPETENCIES] = $allRoles;
            $arr[Capabilities::CAN_DELETE_COMPETENCIES] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_VOCABULARIES] = $allRoles;
            $arr[Capabilities::CAN_CREATE_VOCABULARIES] = $allRoles;
            $arr[Capabilities::CAN_DELETE_VOCABULARIES] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_TERMS] = $allRoles;
            $arr[Capabilities::CAN_CREATE_TERMS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_TERMS] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_INSTRUCTOR_GROUPS] = $allRoles;
            $arr[Capabilities::CAN_CREATE_INSTRUCTOR_GROUPS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_INSTRUCTOR_GROUPS] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_ALL_CURRICULUM_INVENTORY_REPORTS] = $allRoles;
            $arr[Capabilities::CAN_CREATE_CURRICULUM_INVENTORY_REPORTS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_ALL_CURRICULUM_INVENTORY_REPORTS] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_THEIR_CURRICULUM_INVENTORY_REPORTS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_THEIR_CURRICULUM_INVENTORY_REPORTS] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_CURRICULUM_INVENTORY_INSTITUTIONS] = $allRoles;
            $arr[Capabilities::CAN_CREATE_CURRICULUM_INVENTORY_INSTITUTIONS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_CURRICULUM_INVENTORY_INSTITUTIONS] = $allRoles;

            $arr[Capabilities::CAN_UPDATE_LEARNER_GROUPS] = $allRoles;
            $arr[Capabilities::CAN_CREATE_LEARNER_GROUPS] = $allRoles;
            $arr[Capabilities::CAN_DELETE_LEARNER_GROUPS] = $allRoles;

            $this->matrix[$schoolDto->id] = $arr;
        }
    }

    /**
     * @param int $schoolId
     * @param string $capability
     * @param array $roles
     * @return bool
     */
    public function hasPermission(int $schoolId, string $capability, array $roles): bool
    {
        if (!array_key_exists($schoolId, $this->matrix)) {
            return false;
        }
        $schoolPermissions = $this->matrix[$schoolId];
        if (!array_key_exists($capability, $schoolPermissions)) {
            return false;
        };

        $permittedRoles = $schoolPermissions[$capability];

        $hasPermission = false;
        while (!$hasPermission && !empty($roles)) {
            $role = array_pop($roles);
            $hasPermission = in_array($role, $permittedRoles);
        }

        return $hasPermission;
    }

    public function canUpdateCourse(SessionUserInterface $sessionUser, CourseInterface $course): bool
    {
        if ($course->isLocked() || $course->isArchived()) {
            return false;
        }

        $rolesInSchool = $sessionUser->rolesInSchool($course->getSchool()->getId());
        if ($this->hasPermission(
            $course->getSchool()->getId(),
            Capabilities::CAN_UPDATE_ALL_COURSES,
            $rolesInSchool
        )) {
            return true;
        }
        $rolesInCourse = $sessionUser->rolesInCourse($course->getId());
        if ($this->hasPermission(
            $course->getSchool()->getId(),
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

        $rolesInSchool = $sessionUser->rolesInSchool($course->getSchool()->getId());
        if ($this->hasPermission(
            $course->getSchool()->getId(),
            Capabilities::CAN_DELETE_ALL_COURSES,
            $rolesInSchool
        )) {
            return true;
        }
        $rolesInCourse = $sessionUser->rolesInCourse($course->getId());
        if ($this->hasPermission(
            $course->getSchool()->getId(),
            Capabilities::CAN_DELETE_THEIR_COURSES,
            $rolesInCourse
        )) {
            return true;
        }

        return false;
    }

    public function canCreateCourse(SessionUserInterface $sessionUser, SchoolInterface $school): bool
    {
        $rolesInSchool = $sessionUser->rolesInSchool($school->getId());
        if ($this->hasPermission(
            $school->getId(),
            Capabilities::CAN_CREATE_COURSES,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canUnlockCourse(SessionUserInterface $sessionUser, CourseInterface $course): bool
    {
        $rolesInSchool = $sessionUser->rolesInSchool($course->getSchool()->getId());
        if ($this->hasPermission(
            $course->getSchool()->getId(),
            Capabilities::CAN_UNLOCK_ALL_COURSES,
            $rolesInSchool
        )) {
            return true;
        }
        $rolesInCourse = $sessionUser->rolesInCourse($course->getId());
        if ($this->hasPermission(
            $course->getSchool()->getId(),
            Capabilities::CAN_UNLOCK_THEIR_COURSES,
            $rolesInCourse
        )) {
            return true;
        }

        return false;
    }

    public function canUnarchiveCourse(SessionUserInterface $sessionUser, CourseInterface $course): bool
    {
        $rolesInSchool = $sessionUser->rolesInSchool($course->getSchool()->getId());
        if ($this->hasPermission(
            $course->getSchool()->getId(),
            Capabilities::CAN_UNARCHIVE_ALL_COURSES,
            $rolesInSchool
        )) {
            return true;
        }
        $rolesInCourse = $sessionUser->rolesInCourse($course->getId());
        if ($this->hasPermission(
            $course->getSchool()->getId(),
            Capabilities::CAN_UNARCHIVE_THEIR_COURSES,
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
        $rolesInSchool = $sessionUser->rolesInSchool($session->getSchool()->getId());
        if ($this->hasPermission(
            $session->getSchool()->getId(),
            Capabilities::CAN_UPDATE_ALL_SESSIONS,
            $rolesInSchool
        )) {
            return true;
        }
        $rolesInSession = $sessionUser->rolesInSession($session->getId());
        if ($this->hasPermission(
            $session->getSchool()->getId(),
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

        $rolesInSchool = $sessionUser->rolesInSchool($session->getSchool()->getId());
        if ($this->hasPermission(
            $session->getSchool()->getId(),
            Capabilities::CAN_DELETE_ALL_SESSIONS,
            $rolesInSchool
        )) {
            return true;
        }
        $rolesInSession = $sessionUser->rolesInSession($session->getId());
        if ($this->hasPermission(
            $session->getSchool()->getId(),
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

        $rolesInSchool = $sessionUser->rolesInSchool($course->getSchool()->getId());
        if ($this->hasPermission(
            $course->getSchool()->getId(),
            Capabilities::CAN_CREATE_SESSIONS,
            $rolesInSchool
        )) {
            return true;
        }

        return $this->canUpdateCourse($sessionUser, $course);
    }

    public function canUpdateSessionType(SessionUserInterface $sessionUser, int $schoolId): bool
    {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
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
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
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
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
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
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
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
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
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
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
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
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            Capabilities::CAN_UPDATE_ALL_PROGRAMS,
            $rolesInSchool
        )) {
            return true;
        }

        $rolesInProgram = $sessionUser->rolesInProgram($programId);
        if ($this->hasPermission(
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
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            Capabilities::CAN_DELETE_ALL_PROGRAMS,
            $rolesInSchool
        )) {
            return true;
        }

        $rolesInProgram = $sessionUser->rolesInProgram($programId);
        if ($this->hasPermission(
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
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
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

        $rolesInSchool = $sessionUser->rolesInSchool($programYear->getSchool()->getId());
        if ($this->hasPermission(
            $programYear->getSchool()->getId(),
            Capabilities::CAN_UPDATE_ALL_PROGRAM_YEARS,
            $rolesInSchool
        )) {
            return true;
        }
        $rolesInProgramYear = $sessionUser->rolesInProgramYear($programYear->getId());
        if ($this->hasPermission(
            $programYear->getSchool()->getId(),
            Capabilities::CAN_UPDATE_THEIR_PROGRAM_YEARS,
            $rolesInProgramYear
        )) {
            return true;
        }

        return $this->canUpdateProgram(
            $sessionUser,
            $programYear->getProgram()->getId(),
            $programYear->getSchool()->getId()
        );
    }

    public function canDeleteProgramYear(SessionUserInterface $sessionUser, ProgramYearInterface $programYear) : bool
    {
        if ($programYear->isLocked() || $programYear->isArchived()) {
            return false;
        }

        $rolesInSchool = $sessionUser->rolesInSchool($programYear->getSchool()->getId());
        if ($this->hasPermission(
            $programYear->getSchool()->getId(),
            Capabilities::CAN_DELETE_ALL_PROGRAM_YEARS,
            $rolesInSchool
        )) {
            return true;
        }
        $rolesInProgramYear = $sessionUser->rolesInProgramYear($programYear->getId());
        if ($this->hasPermission(
            $programYear->getSchool()->getId(),
            Capabilities::CAN_DELETE_THEIR_PROGRAM_YEARS,
            $rolesInProgramYear
        )) {
            return true;
        }

        return $this->canUpdateProgram(
            $sessionUser,
            $programYear->getProgram()->getId(),
            $programYear->getSchool()->getId()
        );
    }

    public function canCreateProgramYear(SessionUserInterface $sessionUser, ProgramInterface $program): bool
    {
        $rolesInSchool = $sessionUser->rolesInSchool($program->getSchool()->getId());
        if ($this->hasPermission(
            $program->getSchool()->getId(),
            Capabilities::CAN_CREATE_PROGRAM_YEARS,
            $rolesInSchool
        )) {
            return true;
        }

        return $this->canUpdateProgram($sessionUser, $program->getId(), $program->getSchool()->getId());
    }

    public function canUnlockProgramYear(SessionUserInterface $sessionUser, ProgramYearInterface $programYear) : bool
    {
        $rolesInSchool = $sessionUser->rolesInSchool($programYear->getSchool()->getId());
        if ($this->hasPermission(
            $programYear->getSchool()->getId(),
            Capabilities::CAN_UNLOCK_ALL_PROGRAM_YEARS,
            $rolesInSchool
        )) {
            return true;
        }
        $rolesInProgramYear = $sessionUser->rolesInProgramYear($programYear->getId());
        if ($this->hasPermission(
            $programYear->getSchool()->getId(),
            Capabilities::CAN_UNLOCK_THEIR_PROGRAM_YEARS,
            $rolesInProgramYear
        )) {
            return true;
        }

        return $this->canUpdateProgram(
            $sessionUser,
            $programYear->getProgram()->getId(),
            $programYear->getSchool()->getId()
        );
    }

    public function canUnarchiveProgramYear(SessionUserInterface $sessionUser, ProgramYearInterface $programYear) : bool
    {
        $rolesInSchool = $sessionUser->rolesInSchool($programYear->getSchool()->getId());
        if ($this->hasPermission(
            $programYear->getSchool()->getId(),
            Capabilities::CAN_UNARCHIVE_ALL_PROGRAM_YEARS,
            $rolesInSchool
        )) {
            return true;
        }
        $rolesInProgramYear = $sessionUser->rolesInProgramYear($programYear->getId());
        if ($this->hasPermission(
            $programYear->getSchool()->getId(),
            Capabilities::CAN_UNARCHIVE_THEIR_PROGRAM_YEARS,
            $rolesInProgramYear
        )) {
            return true;
        }

        return $this->canUpdateProgram(
            $sessionUser,
            $programYear->getProgram()->getId(),
            $programYear->getSchool()->getId()
        );
    }

    public function canUpdateCohort(SessionUserInterface $sessionUser, CohortInterface $cohort): bool
    {
        if ($cohort->getProgramYear()->isLocked() || $cohort->getProgramYear()->isArchived()) {
            return false;
        }

        $rolesInSchool = $sessionUser->rolesInSchool($cohort->getSchool()->getId());
        if ($this->hasPermission(
            $cohort->getSchool()->getId(),
            Capabilities::CAN_UPDATE_ALL_COHORTS,
            $rolesInSchool
        )) {
            return true;
        }
        $rolesInCohort = $sessionUser->rolesInCohort($cohort->getId());
        if ($this->hasPermission(
            $cohort->getSchool()->getId(),
            Capabilities::CAN_UPDATE_THEIR_COHORTS,
            $rolesInCohort
        )) {
            return true;
        }

        return $this->canUpdateProgram($sessionUser, $cohort->getProgram()->getId(), $cohort->getSchool()->getId());
    }

    public function canDeleteCohort(SessionUserInterface $sessionUser, CohortInterface $cohort): bool
    {
        if ($cohort->getProgramYear()->isLocked() || $cohort->getProgramYear()->isArchived()) {
            return false;
        }

        $rolesInSchool = $sessionUser->rolesInSchool($cohort->getSchool()->getId());
        if ($this->hasPermission(
            $cohort->getSchool()->getId(),
            Capabilities::CAN_DELETE_ALL_COHORTS,
            $rolesInSchool
        )) {
            return true;
        }
        $rolesInCohort = $sessionUser->rolesInCohort($cohort->getId());
        if ($this->hasPermission(
            $cohort->getSchool()->getId(),
            Capabilities::CAN_DELETE_THEIR_COHORTS,
            $rolesInCohort
        )) {
            return true;
        }

        return $this->canUpdateProgram($sessionUser, $cohort->getProgram()->getId(), $cohort->getSchool()->getId());
    }

    public function canCreateCohort(SessionUserInterface $sessionUser, ProgramYearInterface $programYear): bool
    {
        if ($programYear->isLocked() || $programYear->isArchived()) {
            return false;
        }

        $rolesInSchool = $sessionUser->rolesInSchool($programYear->getSchool()->getId());
        if ($this->hasPermission(
            $programYear->getSchool()->getId(),
            Capabilities::CAN_CREATE_COHORTS,
            $rolesInSchool
        )) {
            return true;
        }

        return $this->canUpdateProgram(
            $sessionUser,
            $programYear->getProgram()->getId(),
            $programYear->getSchool()->getId()
        );
    }

    public function canUpdateSchoolConfig(SessionUserInterface $sessionUser, int $schoolId) : bool
    {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
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
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
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
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
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
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            Capabilities::CAN_UPDATE_SCHOOLS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canDeleteSchool(SessionUserInterface $sessionUser, int $schoolId) : bool
    {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            Capabilities::CAN_DELETE_SCHOOLS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canUpdateCompetency(SessionUserInterface $sessionUser, int $schoolId) : bool
    {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
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
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
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
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
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
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
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
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
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
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
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
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
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
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
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
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
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
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
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
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
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
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
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
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            Capabilities::CAN_UPDATE_ALL_CURRICULUM_INVENTORY_REPORTS,
            $rolesInSchool
        )) {
            return true;
        }
        $rolesInCiReport = $sessionUser->rolesInCurriculumInventoryReport($curriculumInventoryReportId);
        if ($this->hasPermission(
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
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            Capabilities::CAN_DELETE_ALL_CURRICULUM_INVENTORY_REPORTS,
            $rolesInSchool
        )) {
            return true;
        }
        $rolesInCiReport = $sessionUser->rolesInCurriculumInventoryReport($curriculumInventoryReportId);
        if ($this->hasPermission(
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
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            Capabilities::CAN_CREATE_CURRICULUM_INVENTORY_REPORTS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canUpdateCurriculumInventoryInstitution(SessionUserInterface $sessionUser, int $schoolId) : bool
    {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
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
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
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
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
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
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
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
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
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
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
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
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
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
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
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
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            Capabilities::CAN_CREATE_USERS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }
}
