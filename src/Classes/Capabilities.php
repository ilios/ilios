<?php

declare(strict_types=1);

namespace App\Classes;

/**
 * Constants-interface that defines all capabilities in the system.
 */
interface Capabilities
{
    /** @var string */
    public const string CAN_UPDATE_ALL_COURSES = 'canUpdateAllCourses';
    /** @var string */
    public const string CAN_DELETE_ALL_COURSES = 'canDeleteAllCourses';
    /** @var string */
    public const string CAN_LOCK_ALL_COURSES = 'canLockAllCourses';
    /** @var string */
    public const string CAN_ARCHIVE_ALL_COURSES = 'canArchiveAllCourses';
    /** @var string */
    public const string CAN_UNLOCK_ALL_COURSES = 'canUnlockAllCourses';
    /** @var string */
    public const string CAN_CREATE_COURSES = 'canCreateCourses';
    /** @var string */
    public const string CAN_UPDATE_THEIR_COURSES = 'canUpdateTheirCourses';
    /** @var string */
    public const string CAN_DELETE_THEIR_COURSES = 'canDeleteTheirCourses';
    /** @var string */
    public const string CAN_LOCK_THEIR_COURSES = 'canLockTheirCourses';
    /** @var string */
    public const string CAN_ARCHIVE_THEIR_COURSES = 'canArchiveTheirCourses';
    /** @var string */
    public const string CAN_UNLOCK_THEIR_COURSES = 'canUnlockTheirCourses';
    /** @var string */
    public const string CAN_UPDATE_ALL_SESSIONS = 'canUpdateAllSessions';
    /** @var string */
    public const string CAN_DELETE_ALL_SESSIONS = 'canDeleteAllSessions';
    /** @var string */
    public const string CAN_CREATE_SESSIONS = 'canCreateSessions';
    /** @var string */
    public const string CAN_UPDATE_THEIR_SESSIONS = 'canUpdateTheirSessions';
    /** @var string */
    public const string CAN_DELETE_THEIR_SESSIONS = 'canDeleteTheirSessions';
    /** @var string */
    public const string CAN_UPDATE_SESSION_TYPES = 'canUpdateSessionTypes';
    /** @var string */
    public const string CAN_DELETE_SESSION_TYPES = 'canDeleteSessionTypes';
    /** @var string */
    public const string CAN_CREATE_SESSION_TYPES = 'canCreateSessionTypes';
    /** @var string */
    public const string CAN_UPDATE_ALL_PROGRAMS = 'canUpdateAllPrograms';
    /** @var string */
    public const string CAN_DELETE_ALL_PROGRAMS = 'canDeleteAllPrograms';
    /** @var string */
    public const string CAN_CREATE_PROGRAMS = 'canCreatePrograms';
    /** @var string */
    public const string CAN_UPDATE_THEIR_PROGRAMS = 'canUpdateTheirPrograms';
    /** @var string */
    public const string CAN_DELETE_THEIR_PROGRAMS = 'canDeleteTheirPrograms';
    /** @var string */
    public const string CAN_UPDATE_ALL_PROGRAM_YEARS = 'canUpdateAllProgramYears';
    /** @var string */
    public const string CAN_DELETE_ALL_PROGRAM_YEARS = 'canDeleteAllProgramYears';
    /** @var string */
    public const string CAN_UNLOCK_ALL_PROGRAM_YEARS = 'canUnlockAllProgramYears';
    /** @var string */
    public const string CAN_LOCK_ALL_PROGRAM_YEARS = 'canLockAllProgramYears';
    /** @var string */
    public const string CAN_ARCHIVE_ALL_PROGRAM_YEARS = 'canArchiveAllProgramYears';
    /** @var string */
    public const string CAN_CREATE_PROGRAM_YEARS = 'canCreateProgramYears';
    /** @var string */
    public const string CAN_UPDATE_THEIR_PROGRAM_YEARS = 'canUpdateTheirProgramYears';
    /** @var string */
    public const string CAN_DELETE_THEIR_PROGRAM_YEARS = 'canDeleteTheirProgramYears';
    /** @var string */
    public const string CAN_LOCK_THEIR_PROGRAM_YEARS = 'canLockTheirProgramYears';
    /** @var string */
    public const string CAN_ARCHIVE_THEIR_PROGRAM_YEARS = 'canArchiveTheirProgramYears';
    /** @var string */
    public const string CAN_UNLOCK_THEIR_PROGRAM_YEARS = 'canUnlockTheirProgramYears';
    /** @var string */
    public const string CAN_UPDATE_SCHOOL_CONFIGS = 'canUpdateSchoolConfigs';
    /** @var string */
    public const string CAN_DELETE_SCHOOL_CONFIGS = 'canDeleteSchoolConfigs';
    /** @var string */
    public const string CAN_CREATE_SCHOOL_CONFIGS = 'canCreateSchoolConfigs';
    /** @var string */
    public const string CAN_UPDATE_SCHOOLS = 'canUpdateSchools';
    /** @var string */
    public const string CAN_UPDATE_COMPETENCIES = 'canUpdateCompetencies';
    /** @var string */
    public const string CAN_DELETE_COMPETENCIES = 'canDeleteCompetencies';
    /** @var string */
    public const string CAN_CREATE_COMPETENCIES = 'canCreateCompetencies';
    /** @var string */
    public const string CAN_UPDATE_VOCABULARIES = 'canUpdateVocabularies';
    /** @var string */
    public const string CAN_DELETE_VOCABULARIES = 'canDeleteVocabularies';
    /** @var string */
    public const string CAN_CREATE_VOCABULARIES = 'canCreateVocabularies';
    /** @var string */
    public const string CAN_UPDATE_TERMS = 'canUpdateTerms';
    /** @var string */
    public const string CAN_DELETE_TERMS = 'canDeleteTerms';
    /** @var string */
    public const string CAN_CREATE_TERMS = 'canCreateTerms';
    /** @var string */
    public const string CAN_UPDATE_INSTRUCTOR_GROUPS = 'canUpdateInstructorGroups';
    /** @var string */
    public const string CAN_DELETE_INSTRUCTOR_GROUPS = 'canDeleteInstructorGroups';
    /** @var string */
    public const string CAN_CREATE_INSTRUCTOR_GROUPS = 'canCreateInstructorGroups';
    /** @var string */
    public const string CAN_UPDATE_ALL_CURRICULUM_INVENTORY_REPORTS = 'canUpdateAllCurriculumInventoryReports';
    /** @var string */
    public const string CAN_DELETE_ALL_CURRICULUM_INVENTORY_REPORTS = 'canDeleteAllCurriculumInventoryReports';
    /** @var string */
    public const string CAN_CREATE_CURRICULUM_INVENTORY_REPORTS = 'canCreateCurriculumInventoryReports';
    /** @var string */
    public const string CAN_UPDATE_THEIR_CURRICULUM_INVENTORY_REPORTS = 'canUpdateTheirCurriculumInventoryReports';
    /** @var string */
    public const string CAN_DELETE_THEIR_CURRICULUM_INVENTORY_REPORTS = 'canDeleteTheirCurriculumInventoryReports';
    /** @var string */
    public const string CAN_CREATE_CURRICULUM_INVENTORY_INSTITUTIONS = 'canCreateCurriculumInventoryInstitutions';
    /** @var string */
    public const string CAN_UPDATE_CURRICULUM_INVENTORY_INSTITUTIONS = 'canUpdateCurriculumInventoryInstitutions';
    /** @var string */
    public const string CAN_DELETE_CURRICULUM_INVENTORY_INSTITUTIONS = 'canDeleteCurriculumInventoryInstitutions';
    /** @var string */
    public const string CAN_CREATE_LEARNER_GROUPS = 'canCreateLearnerGroups';
    /** @var string */
    public const string CAN_UPDATE_LEARNER_GROUPS = 'canUpdateLearnerGroups';
    /** @var string */
    public const string CAN_DELETE_LEARNER_GROUPS = 'canDeleteLearnerGroups';
    /** @var string */
    public const string CAN_CREATE_USERS = 'canCreateUser';
    /** @var string */
    public const string CAN_UPDATE_USERS = 'canUpdateUser';
    /** @var string */
    public const string CAN_DELETE_USERS = 'canDeleteUser';
}
