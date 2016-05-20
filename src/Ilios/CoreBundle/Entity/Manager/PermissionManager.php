<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\PermissionInterface;
use Ilios\CoreBundle\Entity\ProgramInterface;
use Ilios\CoreBundle\Entity\SchoolInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class PermissionManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class PermissionManager extends BaseManager
{
    /**
     * @var string
     */
    const CAN_READ = 'canRead';

    /**
     * @var string
     */
    const CAN_WRITE = 'canWrite';

    /**
     * {@inheritdoc}
     */
    public function findPermissionBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findPermissionsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function updatePermission(
        PermissionInterface $permission,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($permission, $andFlush, $forceId);
    }

    /**
     * {@inheritdoc}
     */
    public function deletePermission(
        PermissionInterface $permission
    ) {
        $this->delete($permission);
    }

    /**
     * {@inheritdoc}
     */
    public function createPermission()
    {
        return $this->create();
    }

    /**
     * Checks if a given user has "read" permissions for a given course.
     * @param UserInterface $user
     * @param null|$courseId
     * @return bool
     */
    public function userHasReadPermissionToCourse(UserInterface $user, $courseId = null)
    {
        return $courseId && $this->userHasPermission($user, self::CAN_READ, 'course', $courseId);
    }

    /**
     * Checks if a given user has "read" permissions for a given program.
     * @param UserInterface $user
     * @param ProgramInterface|null $program
     * @return bool
     */
    public function userHasReadPermissionToProgram(UserInterface $user, ProgramInterface $program = null)
    {
        return $program && $this->userHasPermission($user, self::CAN_READ, 'program', $program->getId());
    }

    /**
     * Checks if a given user has "read" permissions for a given school.
     * @param UserInterface $user
     * @param int|null $schoolId
     * @return bool
     */
    public function userHasReadPermissionToSchool(UserInterface $user, $schoolId = null)
    {
        return $schoolId && $this->userHasPermission($user, self::CAN_READ, 'school', $schoolId);
    }

    /**
     * Checks if a given user has "read" permissions for and in an array of schools.
     * @param UserInterface $user
     * @param ArrayCollection $schools
     * @return bool
     */
    public function userHasReadPermissionToSchools(UserInterface $user, ArrayCollection $schools)
    {
        return $this->userHasPermissionToSchools($user, self::CAN_READ, $schools);
    }

    /**
     * Checks if a given user has "write" permissions for a list of schools
     * @param UserInterface $user
     * @param ArrayCollection $schools
     * @return bool
     */
    public function userHasWritePermissionToSchools(UserInterface $user, ArrayCollection $schools)
    {
        return $this->userHasPermissionToSchools($user, self::CAN_WRITE, $schools);
    }

    /**
     * Checks if a given user has "write" permissions for a given course.
     * @param UserInterface $user
     * @param int|null $courseId
     * @return bool
     */
    public function userHasWritePermissionToCourse(UserInterface $user, $courseId = null)
    {
        return $courseId && $this->userHasPermission($user, self::CAN_WRITE, 'course', $courseId);
    }

    /**
     * Checks if a given user has "write" permissions for a given program.
     * @param UserInterface $user
     * @param ProgramInterface|null $program
     * @return bool
     */
    public function userHasWritePermissionToProgram(UserInterface $user, ProgramInterface $program = null)
    {
        return $program && $this->userHasPermission($user, self::CAN_WRITE, 'program', $program->getId());
    }

    /**
     * Checks if a given user has "write" permissions for a given school.
     * @param UserInterface $user
     * @param int|null $schoolId
     * @return bool
     */
    public function userHasWritePermissionToSchool(UserInterface $user, $schoolId = null)
    {
        return $schoolId && $this->userHasPermission($user, self::CAN_WRITE, 'school', $schoolId);
    }

    /**
     * Checks if a given user has "read" permissions to any courses in a given school.
     * @param UserInterface $user
     * @param SchoolInterface|null $school
     * @return bool
     */
    public function userHasReadPermissionToCoursesInSchool(UserInterface $user, SchoolInterface $school = null)
    {
        if (! $school) {
            return false;
        }

        foreach ($school->getCourses() as $course) {
            if ($this->userHasReadPermissionToCourse($user, $course->getId())) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param UserInterface $user
     * @param string $permission must be either 'canRead' or 'canWrite'.
     * @param string $tableName
     * @param string $tableRowId
     * @return bool
     */
    protected function userHasPermission(UserInterface $user, $permission, $tableName, $tableRowId)
    {
        $criteria = [
            'tableRowId' => $tableRowId,
            'tableName' => $tableName,
            $permission => true,
            'user' => $user,
        ];

        $permission = $this->findPermissionBy($criteria);
        return ! empty($permission);
    }

    /**
     * @param UserInterface $user
     * @param string $permission must be either 'canRead' or 'canWrite'.
     * @param ArrayCollection $schools
     * @return bool
     */
    protected function userHasPermissionToSchools(UserInterface $user, $permission, ArrayCollection $schools)
    {
        $criteria = [
            'tableName' => 'school',
            $permission => true,
            'user' => $user,
        ];

        $permissions = $this->findPermissionsBy($criteria);

        $permittedSchoolIds = array_map(function (PermissionInterface $permission) {
            return $permission->getTableRowId();
        }, $permissions);

        $schoolIds = array_map(function (SchoolInterface $school) {
            return $school->getId();
        }, $schools->toArray());

        $overlap = array_intersect($schoolIds, $permittedSchoolIds);

        return ! empty($overlap);
    }
}
