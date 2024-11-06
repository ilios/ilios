<?php

declare(strict_types=1);

namespace App\Classes;

/**
 * Constant interface, defines all user roles in Ilios.
 * Interface UserRoleInterface
 * @package App\Classes
 */
interface UserRoles
{
    /** @var string */
    public const string COURSE_ADMINISTRATOR = 'courseAdministrator';
    /** @var string */
    public const string COURSE_DIRECTOR = 'courseDirector';
    /** @var string */
    public const string COURSE_INSTRUCTOR = 'courseInstructor';
    /** @var string */
    public const string PROGRAM_DIRECTOR = 'programDirector';
    /** @var string */
    public const string PROGRAM_YEAR_DIRECTOR = 'programYearDirector';
    /** @var string */
    public const string SCHOOL_ADMINISTRATOR = 'schoolAdministrator';
    /** @var string */
    public const string SCHOOL_DIRECTOR = 'schoolDirector';
    /** @var string */
    public const string SESSION_ADMINISTRATOR = 'sessionAdministrator';
    /** @var string */
    public const string SESSION_INSTRUCTOR = 'sessionInstructor';
    /** @var string */
    public const string CURRICULUM_INVENTORY_REPORT_ADMINISTRATOR = 'curriculumInventoryReportAdministrator';
}
