<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\PermissionInterface;
use Ilios\CoreBundle\Entity\ProgramInterface;
use Ilios\CoreBundle\Entity\SchoolInterface;

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
     * Checks if a given user has "read" permissions for a given course.
     * @param SessionUserInterface $user
     * @param null|$courseId
     * @return bool
     */
    public function userHasReadPermissionToCourse(SessionUserInterface $user, $courseId = null)
    {
        return $courseId && $this->userHasPermission($user, self::CAN_READ, 'course', $courseId);
    }

    /**
     * Checks if a given user has "read" permissions for a given program.
     * @param SessionUserInterface $user
     * @param ProgramInterface|null $program
     * @return bool
     */
    public function userHasReadPermissionToProgram(SessionUserInterface $user, ProgramInterface $program = null)
    {
        return $program && $this->userHasPermission($user, self::CAN_READ, 'program', $program->getId());
    }

    /**
     * Checks if a given user has "read" permissions for a given school.
     * @param SessionUserInterface $user
     * @param int|null $schoolId
     * @return bool
     */
    public function userHasReadPermissionToSchool(SessionUserInterface $user, $schoolId = null)
    {
        return $schoolId && $this->userHasPermission($user, self::CAN_READ, 'school', $schoolId);
    }

    /**
     * Checks if a given user has "read" permissions for and in an array of schools.
     * @param SessionUserInterface $user
     * @param ArrayCollection $schools
     * @return bool
     */
    public function userHasReadPermissionToSchools(SessionUserInterface $user, ArrayCollection $schools)
    {
        return $this->userHasPermissionToSchools($user, self::CAN_READ, $schools);
    }

    /**
     * Checks if a given user has "write" permissions for a list of schools
     * @param SessionUserInterface $user
     * @param ArrayCollection $schools
     * @return bool
     */
    public function userHasWritePermissionToSchools(SessionUserInterface $user, ArrayCollection $schools)
    {
        return $this->userHasPermissionToSchools($user, self::CAN_WRITE, $schools);
    }

    /**
     * Checks if a given user has "write" permissions for a given course.
     * @param SessionUserInterface $user
     * @param int|null $courseId
     * @return bool
     */
    public function userHasWritePermissionToCourse(SessionUserInterface $user, $courseId = null)
    {
        return $courseId && $this->userHasPermission($user, self::CAN_WRITE, 'course', $courseId);
    }

    /**
     * Checks if a given user has "write" permissions for a given program.
     * @param SessionUserInterface $user
     * @param ProgramInterface|null $program
     * @return bool
     */
    public function userHasWritePermissionToProgram(SessionUserInterface $user, ProgramInterface $program = null)
    {
        return $program && $this->userHasPermission($user, self::CAN_WRITE, 'program', $program->getId());
    }

    /**
     * Checks if a given user has "write" permissions for a given school.
     * @param SessionUserInterface $user
     * @param int|null $schoolId
     * @return bool
     */
    public function userHasWritePermissionToSchool(SessionUserInterface $user, $schoolId = null)
    {
        return $schoolId && $this->userHasPermission($user, self::CAN_WRITE, 'school', $schoolId);
    }

    /**
     * Checks if a given user has "read" permissions to any courses in a given school.
     * @param SessionUserInterface $user
     * @param SchoolInterface|null $school
     * @return bool
     */
    public function userHasReadPermissionToCoursesInSchool(SessionUserInterface $user, SchoolInterface $school = null)
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
     * @param SessionUserInterface $user
     * @param string $permission must be either 'canRead' or 'canWrite'.
     * @param string $tableName
     * @param string $tableRowId
     * @return bool
     */
    protected function userHasPermission(SessionUserInterface $user, $permission, $tableName, $tableRowId)
    {
        $criteria = [
            'tableRowId' => $tableRowId,
            'tableName' => $tableName,
            $permission => true,
            'user' => $user,
        ];

        $permission = $this->findOneBy($criteria);
        return ! empty($permission);
    }

    /**
     * @param SessionUserInterface $user
     * @param string $permission must be either 'canRead' or 'canWrite'.
     * @param ArrayCollection $schools
     * @return bool
     */
    protected function userHasPermissionToSchools(SessionUserInterface $user, $permission, ArrayCollection $schools)
    {
        $criteria = [
            'tableName' => 'school',
            $permission => true,
            'user' => $user,
        ];

        $permissions = $this->findBy($criteria);

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
