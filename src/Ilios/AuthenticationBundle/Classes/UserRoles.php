<?php

namespace Ilios\AuthenticationBundle\Classes;

/**
 * Constant interface, defines all user roles in Ilios.
 * Interface UserRoleInterface
 * @package Ilios\AuthenticationBundle\Classes
 */
interface UserRoles
{
    /** @var string */
    const COURSE_ADMINISTRATOR = 'courseAdministrator';
    /** @var string */
    const COURSE_DIRECTOR = 'courseDirector';
    /** @var string */
    const COURSE_INSTRUCTOR = 'courseInstructor';
    /** @var string */
    const PROGRAM_DIRECTOR = 'programDirector';
    /** @var string */
    const PROGRAM_YEAR_DIRECTOR = 'programYearDirector';
    /** @var string */
    const SCHOOL_ADMINISTRATOR = 'schoolAdministrator';
    /** @var string */
    const SCHOOL_DIRECTOR = 'schoolDirector';
    /** @var string */
    const SESSION_ADMINISTRATOR = 'sessionAdministrator';
    /** @var string */
    const SESSION_INSTRUCTOR = 'sessionInstructor';
    /** @var string */
    const CURRICULUM_INVENTORY_REPORT_ADMINISTRATOR = 'curriculumInventoryReportAdministrator';
}
