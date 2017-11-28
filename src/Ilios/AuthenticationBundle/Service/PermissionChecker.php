<?php

namespace Ilios\AuthenticationBundle\Service;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\AuthenticationBundle\Classes\UserRoles;
use Ilios\CoreBundle\Entity\DTO\SchoolDTO;
use Ilios\CoreBundle\Entity\Manager\SchoolManager;

class PermissionChecker
{
    /** @var string */
    const CAN_READ_ALL_COURSES = 'canReadAllCourses';
    /** @var string */
    const CAN_UPDATE_ALL_COURSES = 'canUpdateAllCourses';
    /** @var string */
    const CAN_DELETE_ALL_COURSES = 'canDeleteAllCourses';
    /** @var string */
    const CAN_CREATE_COURSES = 'canCreateCourses';
    /** @var string */
    const CAN_READ_THEIR_COURSES = 'canReadTheirCourses';
    /** @var string */
    const CAN_UPDATE_THEIR_COURSES = 'canUpdateTheirCourses';
    /** @var string */
    const CAN_DELETE_THEIR_COURSES = 'canDeleteTheirCourses';
    /** @var string */
    const CAN_READ_ALL_SESSIONS = 'canReadAllSessions';
    /** @var string */
    const CAN_UPDATE_ALL_SESSIONS = 'canUpdateAllSessions';
    /** @var string */
    const CAN_DELETE_ALL_SESSIONS = 'canDeleteAllSessions';
    /** @var string */
    const CAN_CREATE_SESSIONS = 'canCreateSessions';
    /** @var string */
    const CAN_READ_THEIR_SESSIONS = 'canReadTheirSessions';
    /** @var string */
    const CAN_UPDATE_THEIR_SESSIONS = 'canUpdateTheirSessions';
    /** @var string */
    const CAN_DELETE_THEIR_SESSIONS = 'canDeleteTheirSessions';
    /** @var string */
    const CAN_READ_SESSION_TYPES = 'canReadSessionTypes';
    /** @var string */
    const CAN_UPDATE_SESSION_TYPES = 'canUpdateSessionTypes';
    /** @var string */
    const CAN_DELETE_SESSION_TYPES = 'canDeleteSessionTypes';
    /** @var string */
    const CAN_CREATE_SESSION_TYPES = 'canCreateSessionTypes';
    /** @var string */
    const CAN_READ_DEPARTMENTS = 'canReadDepartments';
    /** @var string */
    const CAN_UPDATE_DEPARTMENTS = 'canUpdateDepartments';
    /** @var string */
    const CAN_DELETE_DEPARTMENTS = 'canDeleteDepartments';
    /** @var string */
    const CAN_CREATE_DEPARTMENTS = 'canCreateDepartments';
    /** @var string */
    const CAN_READ_ALL_PROGRAMS = 'canReadAllPrograms';
    /** @var string */
    const CAN_UPDATE_ALL_PROGRAMS = 'canUpdateAllPrograms';
    /** @var string */
    const CAN_DELETE_ALL_PROGRAMS = 'canDeleteAllPrograms';
    /** @var string */
    const CAN_CREATE_PROGRAMS = 'canCreatePrograms';
    /** @var string */
    const CAN_READ_THEIR_PROGRAMS = 'canReadTheirPrograms';
    /** @var string */
    const CAN_UPDATE_THEIR_PROGRAMS = 'canUpdateTheirPrograms';
    /** @var string */
    const CAN_DELETE_THEIR_PROGRAMS = 'canDeleteTheirPrograms';
    /** @var string */
    const CAN_READ_ALL_PROGRAM_YEARS = 'canReadAllProgramYears';
    /** @var string */
    const CAN_UPDATE_ALL_PROGRAM_YEARS = 'canUpdateAllProgramYears';
    /** @var string */
    const CAN_DELETE_ALL_PROGRAM_YEARS = 'canDeleteAllProgramYears';
    /** @var string */
    const CAN_CREATE_PROGRAM_YEARS = 'canCreateProgramYears';
    /** @var string */
    const CAN_READ_THEIR_PROGRAM_YEARS = 'canReadTheirProgramYears';
    /** @var string */
    const CAN_UPDATE_THEIR_PROGRAM_YEARS = 'canUpdateTheirProgramYears';
    /** @var string */
    const CAN_DELETE_THEIR_PROGRAM_YEARS = 'canDeleteTheirProgramYears';
    /** @var string */
    const CAN_READ_ALL_COHORTS = 'canReadAllCohorts';
    /** @var string */
    const CAN_UPDATE_ALL_COHORTS = 'canUpdateAllCohorts';
    /** @var string */
    const CAN_DELETE_ALL_COHORTS = 'canDeleteAllCohorts';
    /** @var string */
    const CAN_CREATE_COHORTS = 'canCreateCohorts';
    /** @var string */
    const CAN_READ_THEIR_COHORTS = 'canReadTheirCohorts';
    /** @var string */
    const CAN_UPDATE_THEIR_COHORTS = 'canUpdateTheirCohorts';
    /** @var string */
    const CAN_DELETE_THEIR_COHORTS = 'canDeleteTheirCohorts';
    /** @var string */
    const CAN_READ_SCHOOL_CONFIGS = 'canReadSchoolConfigs';
    /** @var string */
    const CAN_UPDATE_SCHOOL_CONFIGS = 'canUpdateSchoolConfigs';
    /** @var string */
    const CAN_DELETE_SCHOOL_CONFIGS = 'canDeleteSchoolConfigs';
    /** @var string */
    const CAN_CREATE_SCHOOL_CONFIGS = 'canCreateSchoolConfigs';

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
            $arr[self::CAN_READ_ALL_COURSES] = $allRoles;
            $arr[self::CAN_UPDATE_ALL_COURSES] = $allRoles;
            $arr[self::CAN_CREATE_COURSES] = $allRoles;
            $arr[self::CAN_DELETE_ALL_COURSES] = $allRoles;

            $arr[self::CAN_READ_THEIR_COURSES] = $allRoles;
            $arr[self::CAN_UPDATE_THEIR_COURSES] = $allRoles;
            $arr[self::CAN_DELETE_THEIR_COURSES] = $allRoles;

            $arr[self::CAN_READ_ALL_SESSIONS] = $allRoles;
            $arr[self::CAN_UPDATE_ALL_SESSIONS] = $allRoles;
            $arr[self::CAN_CREATE_SESSIONS] = $allRoles;
            $arr[self::CAN_DELETE_ALL_SESSIONS] = $allRoles;

            $arr[self::CAN_READ_THEIR_SESSIONS] = $allRoles;
            $arr[self::CAN_UPDATE_THEIR_SESSIONS] = $allRoles;
            $arr[self::CAN_DELETE_THEIR_SESSIONS] = $allRoles;

            $arr[self::CAN_READ_SESSION_TYPES] = $allRoles;
            $arr[self::CAN_UPDATE_SESSION_TYPES] = $allRoles;
            $arr[self::CAN_CREATE_SESSION_TYPES] = $allRoles;
            $arr[self::CAN_DELETE_SESSION_TYPES] = $allRoles;

            $arr[self::CAN_READ_DEPARTMENTS] = $allRoles;
            $arr[self::CAN_UPDATE_DEPARTMENTS] = $allRoles;
            $arr[self::CAN_CREATE_DEPARTMENTS] = $allRoles;
            $arr[self::CAN_DELETE_DEPARTMENTS] = $allRoles;

            $arr[self::CAN_READ_SCHOOL_CONFIGS] = $allRoles;
            $arr[self::CAN_UPDATE_SCHOOL_CONFIGS] = $allRoles;
            $arr[self::CAN_CREATE_SCHOOL_CONFIGS] = $allRoles;
            $arr[self::CAN_DELETE_SCHOOL_CONFIGS] = $allRoles;

            $arr[self::CAN_READ_ALL_PROGRAMS] = $allRoles;
            $arr[self::CAN_UPDATE_ALL_PROGRAMS] = $allRoles;
            $arr[self::CAN_CREATE_PROGRAMS] = $allRoles;
            $arr[self::CAN_DELETE_ALL_PROGRAMS] = $allRoles;

            $arr[self::CAN_READ_THEIR_PROGRAMS] = $allRoles;
            $arr[self::CAN_UPDATE_THEIR_PROGRAMS] = $allRoles;
            $arr[self::CAN_DELETE_THEIR_PROGRAMS] = $allRoles;

            $arr[self::CAN_READ_ALL_PROGRAM_YEARS] = $allRoles;
            $arr[self::CAN_UPDATE_ALL_PROGRAM_YEARS] = $allRoles;
            $arr[self::CAN_CREATE_PROGRAM_YEARS] = $allRoles;
            $arr[self::CAN_DELETE_ALL_PROGRAM_YEARS] = $allRoles;

            $arr[self::CAN_READ_THEIR_PROGRAM_YEARS] = $allRoles;
            $arr[self::CAN_UPDATE_THEIR_PROGRAM_YEARS] = $allRoles;
            $arr[self::CAN_DELETE_THEIR_PROGRAM_YEARS] = $allRoles;

            $arr[self::CAN_READ_ALL_COHORTS] = $allRoles;
            $arr[self::CAN_UPDATE_ALL_COHORTS] = $allRoles;
            $arr[self::CAN_CREATE_COHORTS] = $allRoles;
            $arr[self::CAN_DELETE_ALL_COHORTS] = $allRoles;

            $arr[self::CAN_READ_THEIR_COHORTS] = $allRoles;
            $arr[self::CAN_UPDATE_THEIR_COHORTS] = $allRoles;
            $arr[self::CAN_DELETE_THEIR_COHORTS] = $allRoles;

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

    public function canReadCourse(SessionUserInterface $sessionUser, int $courseId, int $schoolId): bool
    {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_READ_ALL_COURSES,
            $rolesInSchool
        )) {
            return true;
        }
        $rolesInCourse = $sessionUser->rolesInCourse($courseId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_READ_THEIR_COURSES,
            $rolesInCourse
        )) {
            return true;
        }

        return false;
    }

    public function canUpdateCourse(SessionUserInterface $sessionUser, int $courseId, int $schoolId): bool
    {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_UPDATE_ALL_COURSES,
            $rolesInSchool
        )) {
            return true;
        }
        $rolesInCourse = $sessionUser->rolesInCourse($courseId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_UPDATE_THEIR_COURSES,
            $rolesInCourse
        )) {
            return true;
        }

        return false;
    }

    public function canDeleteCourse(SessionUserInterface $sessionUser, int $courseId, int $schoolId): bool
    {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_DELETE_ALL_COURSES,
            $rolesInSchool
        )) {
            return true;
        }
        $rolesInCourse = $sessionUser->rolesInCourse($courseId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_DELETE_THEIR_COURSES,
            $rolesInCourse
        )) {
            return true;
        }

        return false;
    }

    public function canCreateCourse(SessionUserInterface $sessionUser, int $schoolId): bool
    {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_CREATE_COURSES,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canReadSession(
        SessionUserInterface $sessionUser,
        int $sessionId,
        int $courseId,
        int $schoolId
    ): bool {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_READ_ALL_SESSIONS,
            $rolesInSchool
        )) {
            return true;
        }
        $rolesInSession = $sessionUser->rolesInSession($sessionId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_READ_THEIR_SESSIONS,
            $rolesInSession
        )) {
            return true;
        }

        return $this->canReadCourse($sessionUser, $courseId, $schoolId);
    }

    public function canUpdateSession(
        SessionUserInterface $sessionUser,
        int $sessionId,
        int $courseId,
        int $schoolId
    ): bool {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_UPDATE_ALL_SESSIONS,
            $rolesInSchool
        )) {
            return true;
        }
        $rolesInSession = $sessionUser->rolesInSession($sessionId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_UPDATE_THEIR_SESSIONS,
            $rolesInSession
        )) {
            return true;
        }

        return $this->canUpdateCourse($sessionUser, $courseId, $schoolId);
    }

    public function canDeleteSession(
        SessionUserInterface $sessionUser,
        int $sessionId,
        int $courseId,
        int $schoolId
    ): bool {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_DELETE_ALL_SESSIONS,
            $rolesInSchool
        )) {
            return true;
        }
        $rolesInSession = $sessionUser->rolesInSession($sessionId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_DELETE_THEIR_SESSIONS,
            $rolesInSession
        )) {
            return true;
        }

        return $this->canUpdateCourse($sessionUser, $courseId, $schoolId);
    }

    public function canCreateSession(
        SessionUserInterface $sessionUser,
        int $courseId,
        int $schoolId
    ): bool {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_CREATE_SESSIONS,
            $rolesInSchool
        )) {
            return true;
        }

        return $this->canUpdateCourse($sessionUser, $courseId, $schoolId);
    }

    public function canReadSessionType(SessionUserInterface $sessionUser, int $schoolId): bool
    {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_READ_SESSION_TYPES,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canUpdateSessionType(SessionUserInterface $sessionUser, int $schoolId): bool
    {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_UPDATE_SESSION_TYPES,
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
            PermissionChecker::CAN_DELETE_SESSION_TYPES,
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
            PermissionChecker::CAN_CREATE_SESSION_TYPES,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canReadDepartment(SessionUserInterface $sessionUser, int $schoolId) : bool
    {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_READ_DEPARTMENTS,
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
            PermissionChecker::CAN_UPDATE_DEPARTMENTS,
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
            PermissionChecker::CAN_DELETE_DEPARTMENTS,
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
            PermissionChecker::CAN_CREATE_DEPARTMENTS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canReadProgram(SessionUserInterface $sessionUser, int $programId, int $schoolId) : bool
    {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_READ_ALL_PROGRAMS,

            $rolesInSchool
        )) {
            return true;
        }

        $rolesInProgram = $sessionUser->rolesInProgram($programId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_READ_THEIR_PROGRAMS,
            $rolesInProgram
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
            PermissionChecker::CAN_UPDATE_ALL_PROGRAMS,
            $rolesInSchool
        )) {
            return true;
        }

        $rolesInProgram = $sessionUser->rolesInProgram($programId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_UPDATE_THEIR_PROGRAMS,
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
            PermissionChecker::CAN_DELETE_ALL_PROGRAMS,
            $rolesInSchool
        )) {
            return true;
        }

        $rolesInProgram = $sessionUser->rolesInProgram($programId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_DELETE_THEIR_PROGRAMS,
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
            PermissionChecker::CAN_CREATE_PROGRAMS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canReadProgramYear(
        SessionUserInterface $sessionUser,
        int $programYearId,
        int $programId,
        int $schoolId
    ) : bool {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_READ_ALL_PROGRAM_YEARS,
            $rolesInSchool
        )) {
            return true;
        }
        $rolesInProgramYear = $sessionUser->rolesInProgramYear($programYearId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_READ_THEIR_PROGRAM_YEARS,
            $rolesInProgramYear
        )) {
            return true;
        }

        return $this->canReadProgram($sessionUser, $programId, $schoolId);
    }

    public function canUpdateProgramYear(
        SessionUserInterface $sessionUser,
        int $programYearId,
        int $programId,
        int $schoolId
    ) : bool {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_UPDATE_ALL_PROGRAM_YEARS,
            $rolesInSchool
        )) {
            return true;
        }
        $rolesInProgramYear = $sessionUser->rolesInProgramYear($programYearId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_UPDATE_THEIR_PROGRAM_YEARS,
            $rolesInProgramYear
        )) {
            return true;
        }

        return $this->canUpdateProgram($sessionUser, $programId, $schoolId);
    }

    public function canDeleteProgramYear(
        SessionUserInterface $sessionUser,
        int $programYearId,
        int $programId,
        int $schoolId
    ) : bool {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_DELETE_ALL_PROGRAM_YEARS,
            $rolesInSchool
        )) {
            return true;
        }
        $rolesInProgramYear = $sessionUser->rolesInProgramYear($programYearId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_DELETE_THEIR_PROGRAM_YEARS,
            $rolesInProgramYear
        )) {
            return true;
        }

        return $this->canUpdateProgram($sessionUser, $programId, $schoolId);
    }

    public function canCreateProgramYear(
        SessionUserInterface $sessionUser,
        int $programId,
        int $schoolId
    ) : bool {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_CREATE_PROGRAM_YEARS,
            $rolesInSchool
        )) {
            return true;
        }

        return $this->canUpdateProgram($sessionUser, $programId, $schoolId);
    }

    public function canReadCohort(
        SessionUserInterface $sessionUser,
        int $cohortId,
        int $programId,
        int $schoolId
    ) : bool {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_READ_ALL_COHORTS,
            $rolesInSchool
        )) {
            return true;
        }
        $rolesInCohort = $sessionUser->rolesInCohort($cohortId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_READ_THEIR_COHORTS,
            $rolesInCohort
        )) {
            return true;
        }

        return $this->canReadProgram($sessionUser, $programId, $schoolId);
    }

    public function canUpdateCohort(
        SessionUserInterface $sessionUser,
        int $cohortId,
        int $programId,
        int $schoolId
    ) : bool {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_UPDATE_ALL_COHORTS,
            $rolesInSchool
        )) {
            return true;
        }
        $rolesInCohort = $sessionUser->rolesInCohort($cohortId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_UPDATE_THEIR_COHORTS,
            $rolesInCohort
        )) {
            return true;
        }

        return $this->canUpdateProgram($sessionUser, $programId, $schoolId);
    }

    public function canDeleteCohort(
        SessionUserInterface $sessionUser,
        int $cohortId,
        int $programId,
        int $schoolId
    ) : bool {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_DELETE_ALL_COHORTS,
            $rolesInSchool
        )) {
            return true;
        }
        $rolesInCohort = $sessionUser->rolesInCohort($cohortId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_DELETE_THEIR_COHORTS,
            $rolesInCohort
        )) {
            return true;
        }

        return $this->canUpdateProgram($sessionUser, $programId, $schoolId);
    }

    public function canCreateCohort(
        SessionUserInterface $sessionUser,
        int $programId,
        int $schoolId
    ) : bool {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_CREATE_COHORTS,
            $rolesInSchool
        )) {
            return true;
        }

        return $this->canUpdateProgram($sessionUser, $programId, $schoolId);
    }
}
