<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\PermissionInterface;
use Ilios\CoreBundle\Entity\ProgramInterface;
use Ilios\CoreBundle\Entity\SchoolInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Interface PermissionManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface PermissionManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return PermissionInterface
     */
    public function findPermissionBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return PermissionInterface[]
     */
    public function findPermissionsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param PermissionInterface $permission
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updatePermission(
        PermissionInterface $permission,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param PermissionInterface $permission
     *
     * @return void
     */
    public function deletePermission(
        PermissionInterface $permission
    );

    /**
     * @return PermissionInterface
     */
    public function createPermission();

    /**
     * Checks if a given user has "read" permissions for a given course.
     * @param UserInterface $user
     * @param null|$courseId
     * @return bool
     */
    public function userHasReadPermissionToCourse(UserInterface $user, $courseId = null);

    /**
     * Checks if a given user has "read" permissions for a given program.
     * @param UserInterface $user
     * @param ProgramInterface|null $program
     * @return bool
     */
    public function userHasReadPermissionToProgram(UserInterface $user, ProgramInterface $program = null);
    
    /**
     * Checks if a given user has "read" permissions for a given school.
     * @param UserInterface $user
     * @param int|null $schoolId
     * @return bool
     */
    public function userHasReadPermissionToSchool(UserInterface $user, $schoolId = null);
    
    /**
     * Checks if a given user has "read" permissions for and in an array of schools.
     * @param UserInterface $user
     * @param ArrayCollection $schools
     * @return bool
     */
    public function userHasReadPermissionToSchools(UserInterface $user, ArrayCollection $schools);
     
    /**
    * Checks if a given user has "write" permissions for a list of schools
    * @param UserInterface $user
    * @param ArrayCollection $schools
    * @return bool
    */
    public function userHasWritePermissionToSchools(UserInterface $user, ArrayCollection $schools);

    /**
     * Checks if a given user has "write" permissions for a given course.
     * @param UserInterface $user
     * @param int|null $courseId
     * @return bool
     */
    public function userHasWritePermissionToCourse(UserInterface $user, $courseId = null);

    /**
     * Checks if a given user has "write" permissions for a given program.
     * @param UserInterface $user
     * @param ProgramInterface|null $program
     * @return bool
     */
    public function userHasWritePermissionToProgram(UserInterface $user, ProgramInterface $program = null);

    /**
     * Checks if a given user has "write" permissions for a given school.
     * @param UserInterface $user
     * @param int|null $schoolId
     * @return bool
     */
    public function userHasWritePermissionToSchool(UserInterface $user, $schoolId = null);

    /**
     * Checks if a given user has "read" permissions to any courses in a given school.
     * @param UserInterface $user
     * @param SchoolInterface|null $school
     * @return bool
     */
    public function userHasReadPermissionToCoursesInSchool(UserInterface $user, SchoolInterface $school = null);
}
