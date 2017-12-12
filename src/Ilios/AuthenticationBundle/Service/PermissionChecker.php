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
    /** @var string */
    const CAN_READ_SCHOOLS = 'canReadSchools';
    /** @var string */
    const CAN_UPDATE_SCHOOLS = 'canUpdateSchools';
    /** @var string */
    const CAN_DELETE_SCHOOLS = 'canDeleteSchools';
    /** @var string */
    const CAN_READ_COMPETENCIES = 'canReadCompetencies';
    /** @var string */
    const CAN_UPDATE_COMPETENCIES = 'canUpdateCompetencies';
    /** @var string */
    const CAN_DELETE_COMPETENCIES = 'canDeleteCompetencies';
    /** @var string */
    const CAN_CREATE_COMPETENCIES = 'canCreateCompetencies';
    /** @var string */
    const CAN_READ_VOCABULARIES = 'canReadVocabularies';
    /** @var string */
    const CAN_UPDATE_VOCABULARIES = 'canUpdateVocabularies';
    /** @var string */
    const CAN_DELETE_VOCABULARIES = 'canDeleteVocabularies';
    /** @var string */
    const CAN_CREATE_VOCABULARIES = 'canCreateVocabularies';
    /** @var string */
    const CAN_READ_TERMS = 'canReadTerms';
    /** @var string */
    const CAN_UPDATE_TERMS = 'canUpdateTerms';
    /** @var string */
    const CAN_DELETE_TERMS = 'canDeleteTerms';
    /** @var string */
    const CAN_CREATE_TERMS = 'canCreateTerms';
    /** @var string */
    const CAN_READ_INSTRUCTOR_GROUPS = 'canReadInstructorGroups';
    /** @var string */
    const CAN_UPDATE_INSTRUCTOR_GROUPS = 'canUpdateInstructorGroups';
    /** @var string */
    const CAN_DELETE_INSTRUCTOR_GROUPS = 'canDeleteInstructorGroups';
    /** @var string */
    const CAN_CREATE_INSTRUCTOR_GROUPS = 'canCreateInstructorGroups';
    /** @var string */
    const CAN_READ_ALL_CURRICULUM_INVENTORY_REPORTS = 'canReadAllCurriculumInventoryReports';
    /** @var string */
    const CAN_UPDATE_ALL_CURRICULUM_INVENTORY_REPORTS = 'canUpdateAllCurriculumInventoryReports';
    /** @var string */
    const CAN_DELETE_ALL_CURRICULUM_INVENTORY_REPORTS = 'canDeleteAllCurriculumInventoryReports';
    /** @var string */
    const CAN_CREATE_CURRICULUM_INVENTORY_REPORTS = 'canCreateCurriculumInventoryReports';
    /** @var string */
    const CAN_READ_THEIR_CURRICULUM_INVENTORY_REPORTS = 'canReadTheirCurriculumInventoryReports';
    /** @var string */
    const CAN_UPDATE_THEIR_CURRICULUM_INVENTORY_REPORTS = 'canUpdateTheirCurriculumInventoryReports';
    /** @var string */
    const CAN_DELETE_THEIR_CURRICULUM_INVENTORY_REPORTS = 'canDeleteTheirCurriculumInventoryReports';
    /** @var string */
    const CAN_CREATE_CURRICULUM_INVENTORY_INSTITUTIONS = 'canCreateCurriculumInventoryInstitutions';
    /** @var string */
    const CAN_READ_CURRICULUM_INVENTORY_INSTITUTIONS = 'canReadCurriculumInventoryInstitutions';
    /** @var string */
    const CAN_UPDATE_CURRICULUM_INVENTORY_INSTITUTIONS = 'canUpdateCurriculumInventoryInstitutions';
    /** @var string */
    const CAN_DELETE_CURRICULUM_INVENTORY_INSTITUTIONS = 'canDeleteCurriculumInventoryInstitutions';
    /** @var string */
    const CAN_CREATE_LEARNER_GROUPS = 'canCreateLearnerGroups';
    /** @var string */
    const CAN_READ_LEARNER_GROUPS = 'canReadLearnerGroups';
    /** @var string */
    const CAN_UPDATE_LEARNER_GROUPS = 'canUpdateLearnerGroups';
    /** @var string */
    const CAN_DELETE_LEARNER_GROUPS = 'canDeleteLearnerGroups';

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

            $arr[self::CAN_READ_SCHOOL_CONFIGS] = $allRoles;
            $arr[self::CAN_UPDATE_SCHOOL_CONFIGS] = $allRoles;
            $arr[self::CAN_CREATE_SCHOOL_CONFIGS] = $allRoles;
            $arr[self::CAN_DELETE_SCHOOL_CONFIGS] = $allRoles;

            $arr[self::CAN_READ_SCHOOLS] = $allRoles;
            $arr[self::CAN_UPDATE_SCHOOLS] = $allRoles;
            $arr[self::CAN_DELETE_SCHOOLS] = $allRoles;

            $arr[self::CAN_READ_COMPETENCIES] = $allRoles;
            $arr[self::CAN_UPDATE_COMPETENCIES] = $allRoles;
            $arr[self::CAN_CREATE_COMPETENCIES] = $allRoles;
            $arr[self::CAN_DELETE_COMPETENCIES] = $allRoles;

            $arr[self::CAN_READ_VOCABULARIES] = $allRoles;
            $arr[self::CAN_UPDATE_VOCABULARIES] = $allRoles;
            $arr[self::CAN_CREATE_VOCABULARIES] = $allRoles;
            $arr[self::CAN_DELETE_VOCABULARIES] = $allRoles;

            $arr[self::CAN_READ_TERMS] = $allRoles;
            $arr[self::CAN_UPDATE_TERMS] = $allRoles;
            $arr[self::CAN_CREATE_TERMS] = $allRoles;
            $arr[self::CAN_DELETE_TERMS] = $allRoles;

            $arr[self::CAN_READ_INSTRUCTOR_GROUPS] = $allRoles;
            $arr[self::CAN_UPDATE_INSTRUCTOR_GROUPS] = $allRoles;
            $arr[self::CAN_CREATE_INSTRUCTOR_GROUPS] = $allRoles;
            $arr[self::CAN_DELETE_INSTRUCTOR_GROUPS] = $allRoles;

            $arr[self::CAN_READ_ALL_CURRICULUM_INVENTORY_REPORTS] = $allRoles;
            $arr[self::CAN_UPDATE_ALL_CURRICULUM_INVENTORY_REPORTS] = $allRoles;
            $arr[self::CAN_CREATE_CURRICULUM_INVENTORY_REPORTS] = $allRoles;
            $arr[self::CAN_DELETE_ALL_CURRICULUM_INVENTORY_REPORTS] = $allRoles;

            $arr[self::CAN_READ_THEIR_CURRICULUM_INVENTORY_REPORTS] = $allRoles;
            $arr[self::CAN_UPDATE_THEIR_CURRICULUM_INVENTORY_REPORTS] = $allRoles;
            $arr[self::CAN_DELETE_THEIR_CURRICULUM_INVENTORY_REPORTS] = $allRoles;

            $arr[self::CAN_READ_CURRICULUM_INVENTORY_INSTITUTIONS] = $allRoles;
            $arr[self::CAN_UPDATE_CURRICULUM_INVENTORY_INSTITUTIONS] = $allRoles;
            $arr[self::CAN_CREATE_CURRICULUM_INVENTORY_INSTITUTIONS] = $allRoles;
            $arr[self::CAN_DELETE_CURRICULUM_INVENTORY_INSTITUTIONS] = $allRoles;

            $arr[self::CAN_READ_LEARNER_GROUPS] = $allRoles;
            $arr[self::CAN_UPDATE_LEARNER_GROUPS] = $allRoles;
            $arr[self::CAN_CREATE_LEARNER_GROUPS] = $allRoles;
            $arr[self::CAN_DELETE_LEARNER_GROUPS] = $allRoles;

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

    public function canReadSchoolConfig(SessionUserInterface $sessionUser, int $schoolId) : bool
    {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_READ_SCHOOL_CONFIGS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canUpdateSchoolConfig(SessionUserInterface $sessionUser, int $schoolId) : bool
    {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_UPDATE_SCHOOL_CONFIGS,
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
            PermissionChecker::CAN_DELETE_SCHOOL_CONFIGS,
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
            PermissionChecker::CAN_CREATE_SCHOOL_CONFIGS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canReadSchools(SessionUserInterface $sessionUser, int $schoolId) : bool
    {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_READ_SCHOOLS,
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
            PermissionChecker::CAN_UPDATE_SCHOOLS,
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
            PermissionChecker::CAN_DELETE_SCHOOLS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canReadCompetency(SessionUserInterface $sessionUser, int $schoolId) : bool
    {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_READ_COMPETENCIES,
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
            PermissionChecker::CAN_UPDATE_COMPETENCIES,
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
            PermissionChecker::CAN_DELETE_COMPETENCIES,
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
            PermissionChecker::CAN_CREATE_COMPETENCIES,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canReadVocabulary(SessionUserInterface $sessionUser, int $schoolId): bool
    {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_READ_VOCABULARIES,
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
            PermissionChecker::CAN_UPDATE_VOCABULARIES,
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
            PermissionChecker::CAN_DELETE_VOCABULARIES,
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
            PermissionChecker::CAN_CREATE_VOCABULARIES,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canReadTerm(SessionUserInterface $sessionUser, int $schoolId): bool
    {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_READ_TERMS,
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
            PermissionChecker::CAN_UPDATE_TERMS,
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
            PermissionChecker::CAN_DELETE_TERMS,
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
            PermissionChecker::CAN_CREATE_TERMS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canReadInstructorGroup(SessionUserInterface $sessionUser, int $schoolId): bool
    {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_READ_INSTRUCTOR_GROUPS,
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
            PermissionChecker::CAN_UPDATE_INSTRUCTOR_GROUPS,
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
            PermissionChecker::CAN_DELETE_INSTRUCTOR_GROUPS,
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
            PermissionChecker::CAN_CREATE_INSTRUCTOR_GROUPS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canReadCurriculumInventoryReport(
        SessionUserInterface $sessionUser,
        int $curriculumInventoryReportId,
        int $schoolId
    ): bool {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_READ_ALL_CURRICULUM_INVENTORY_REPORTS,
            $rolesInSchool
        )) {
            return true;
        }
        $rolesInCiReport = $sessionUser->rolesInCurriculumInventoryReport($curriculumInventoryReportId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_READ_THEIR_CURRICULUM_INVENTORY_REPORTS,
            $rolesInCiReport
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
            PermissionChecker::CAN_UPDATE_ALL_CURRICULUM_INVENTORY_REPORTS,
            $rolesInSchool
        )) {
            return true;
        }
        $rolesInCiReport = $sessionUser->rolesInCurriculumInventoryReport($curriculumInventoryReportId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_UPDATE_THEIR_CURRICULUM_INVENTORY_REPORTS,
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
            PermissionChecker::CAN_DELETE_ALL_CURRICULUM_INVENTORY_REPORTS,
            $rolesInSchool
        )) {
            return true;
        }
        $rolesInCiReport = $sessionUser->rolesInCurriculumInventoryReport($curriculumInventoryReportId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_DELETE_THEIR_CURRICULUM_INVENTORY_REPORTS,
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
            PermissionChecker::CAN_CREATE_CURRICULUM_INVENTORY_REPORTS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canReadCurriculumInventoryInstitution(SessionUserInterface $sessionUser, int $schoolId) : bool
    {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_READ_CURRICULUM_INVENTORY_INSTITUTIONS,
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
            PermissionChecker::CAN_UPDATE_CURRICULUM_INVENTORY_INSTITUTIONS,
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
            PermissionChecker::CAN_DELETE_CURRICULUM_INVENTORY_INSTITUTIONS,
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
            PermissionChecker::CAN_CREATE_CURRICULUM_INVENTORY_INSTITUTIONS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canReadLearnerGroup(SessionUserInterface $sessionUser, int $schoolId) : bool
    {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_READ_LEARNER_GROUPS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canUpdateLearnerGroup(SessionUserInterface $sessionUser, int $schoolId) : bool
    {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_UPDATE_LEARNER_GROUPS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canDeleteLearnerGroup(SessionUserInterface $sessionUser, int $schoolId) : bool
    {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_DELETE_LEARNER_GROUPS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }

    public function canCreateLearnerGroup(SessionUserInterface $sessionUser, int $schoolId) : bool
    {
        $rolesInSchool = $sessionUser->rolesInSchool($schoolId);
        if ($this->hasPermission(
            $schoolId,
            PermissionChecker::CAN_CREATE_LEARNER_GROUPS,
            $rolesInSchool
        )) {
            return true;
        }

        return false;
    }
}
