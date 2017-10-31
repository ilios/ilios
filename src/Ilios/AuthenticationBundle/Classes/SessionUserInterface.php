<?php

namespace Ilios\AuthenticationBundle\Classes;

use Ilios\CoreBundle\Entity\SchoolInterface;
use Ilios\CoreBundle\Entity\UserInterface as IliosUserInterface;
use Symfony\Component\Security\Core\Encoder\EncoderAwareInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use DateTime;

interface SessionUserInterface extends UserInterface, EquatableInterface, EncoderAwareInterface
{
    /**
     * Utility method, determines if the user has any of the given roles.
     * @param array $eligibleRoles a list of role names
     *
     * @return bool TRUE if the user has at least one of the roles, FALSE otherwise.
     */
    public function hasRole(array $eligibleRoles);

    /**
     * Is this user a root user
     *
     * @return boolean
     */
    public function isRoot();

    /**
     * Is this user enabled
     *
     * @return boolean
     */
    public function isEnabled();

    /**
     * When is the last moment that a users token would be valid
     *
     * @return DateTime | null
     */
    public function tokenNotValidBefore();

    /**
     * Get user's id
     *
     * @return integer
     */
    public function getId();

    /**
     * Get user's primary school id
     *
     * @return integer
     */
    public function getSchoolId();

    /**
     * Check if a user can read a school
     *
     * @param $schoolId
     *
     * @return boolean
     */
    public function hasReadPermissionToSchool($schoolId);

    /**
     * Check if a user can read a school
     *
     * @param array $schoolIds
     *
     * @return boolean
     */
    public function hasReadPermissionToSchools(array $schoolIds);

    /**
     * Check if a user can read a program
     *
     * @param $programId
     *
     * @return boolean
     */
    public function hasReadPermissionToProgram($programId);

    /**
     * Check if a user can read a course
     *
     * @param $courseId
     *
     * @return boolean
     */
    public function hasReadPermissionToCourse($courseId);

    /**
     * Check if a user can write a school
     *
     * @param $schoolId
     *
     * @return boolean
     */
    public function hasWritePermissionToSchool($schoolId);

    /**
     * Check if a user can write a school
     *
     * @param array $schoolIds
     *
     * @return boolean
     */
    public function hasWritePermissionToSchools(array $schoolIds);

    /**
     * Check if a user can write a program
     *
     * @param $programId
     *
     * @return boolean
     */
    public function hasWritePermissionToProgram($programId);

    /**
     * Check if a user can write a course
     *
     * @param $courseId
     *
     * @return boolean
     */
    public function hasWritePermissionToCourse($courseId);

    /**
     * Check if the passed user is our session user by id
     *
     * @param IliosUserInterface $user
     * @return bool
     */
    public function isTheUser(IliosUserInterface $user);

    /**
     * Check if the passed school is our user's primary school by id
     *
     * @param SchoolInterface $school
     * @return bool
     */
    public function isThePrimarySchool(SchoolInterface $school);

    /**
     * Check if a user is a director of a course
     *
     * @param $courseId
     *
     * @return boolean
     */
    public function isDirectingCourse(int $courseId);
    public function isAdministeringCourse(int $courseId): bool;

    public function isDirectingSchool(int $schoolId) : bool;
    public function isAdministeringSchool(int $schoolId) : bool;
    public function isDirectingCourseInSchool(int $schoolId) : bool;
    public function isAdministeringCourseInSchool(int $schoolId) : bool;
    public function isAdministeringSessionInSchool(int $schoolId) : bool;
    public function isTeachingCourseInSchool(int $schoolId) : bool;
    public function isTeachingCourse(int $courseId) : bool;
    public function rolesInSchool(int $schoolId): array;
    public function rolesInCourse(int $courseId): array;
    public function isAdministeringSessionInCourse(int $courseId) : bool;
}
