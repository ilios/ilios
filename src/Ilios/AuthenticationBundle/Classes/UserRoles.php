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
    const COURSE_DIRECTOR = 'courseDirector';
    /** @var string */
    const COURSE_ADMINISTRATOR = 'courseAdministrator';
    /** @var string */
    const COURSE_INSTRUCTOR = 'courseInstructor';
    /** @var string */
    const SESSION_ADMINISTRATOR = 'sessionAdministrator';
    /** @var string */
    const SCHOOL_ADMINISTRATOR = 'schoolAdministrator';
    /** @var string */
    const SCHOOL_DIRECTOR = 'schoolDirector';
}
