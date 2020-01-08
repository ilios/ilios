<?php

namespace App\Classes;

/**
 * Constant interface, defines all user roles in Ilios.
 * Interface UserRoleInterface
 * @package App\Classes
 */
interface UserRoles
{
    /** @var string */
    public const COURSE_ADMINISTRATOR = 'courseAdministrator';
    /** @var string */
    public const COURSE_DIRECTOR = 'courseDirector';
    /** @var string */
    public const COURSE_INSTRUCTOR = 'courseInstructor';
    /** @var string */
    public const PROGRAM_DIRECTOR = 'programDirector';
    /** @var string */
    public const PROGRAM_YEAR_DIRECTOR = 'programYearDirector';
    /** @var string */
    public const SCHOOL_ADMINISTRATOR = 'schoolAdministrator';
    /** @var string */
    public const SCHOOL_DIRECTOR = 'schoolDirector';
    /** @var string */
    public const SESSION_ADMINISTRATOR = 'sessionAdministrator';
    /** @var string */
    public const SESSION_INSTRUCTOR = 'sessionInstructor';
    /** @var string */
    public const CURRICULUM_INVENTORY_REPORT_ADMINISTRATOR = 'curriculumInventoryReportAdministrator';
}
