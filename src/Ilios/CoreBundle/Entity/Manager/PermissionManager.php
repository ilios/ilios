<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\CohortInterface;
use Ilios\CoreBundle\Entity\CourseInterface;
use Ilios\CoreBundle\Entity\PermissionInterface;
use Ilios\CoreBundle\Entity\ProgramInterface;
use Ilios\CoreBundle\Entity\SchoolInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class PermissionManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class PermissionManager extends AbstractManager implements PermissionManagerInterface
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
        return $this->getRepository()->findOneBy($criteria, $orderBy);
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
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function updatePermission(
        PermissionInterface $permission,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($permission);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($permission));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deletePermission(
        PermissionInterface $permission
    ) {
        $this->em->remove($permission);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createPermission()
    {
        $class = $this->getClass();

        return new $class();
    }

    /**
     * {@inheritdoc}
     */
    public function userHasReadPermissionToCourse(UserInterface $user, $courseId = null)
    {
        return $courseId && $this->userHasPermission($user, self::CAN_READ, 'course', $courseId);
    }

    /**
     * {@inheritdoc}
     */
    public function userHasReadPermissionToProgram(UserInterface $user, ProgramInterface $program = null)
    {
        return $program && $this->userHasPermission($user, self::CAN_READ, 'program', $program->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function userHasReadPermissionToSchool(UserInterface $user, $schoolId = null)
    {
        return $schoolId && $this->userHasPermission($user, self::CAN_READ, 'school', $schoolId);
    }

    /**
     * {@inheritdoc}
     */
    public function userHasReadPermissionToSchools(UserInterface $user, ArrayCollection $schools)
    {
        return $this->userHasPermissionToSchools($user, self::CAN_READ, $schools);
    }

    /**
     * {@inheritdoc}
     */
    public function userHasWritePermissionToSchools(UserInterface $user, ArrayCollection $schools)
    {
        return $this->userHasPermissionToSchools($user, self::CAN_WRITE, $schools);
    }

    /**
     * {@inheritdoc}
     */
    public function userHasWritePermissionToCourse(UserInterface $user, $courseId = null)
    {
        return $courseId && $this->userHasPermission($user, self::CAN_WRITE, 'course', $courseId);
    }

    /**
     * {@inheritdoc}
     */
    public function userHasWritePermissionToProgram(UserInterface $user, ProgramInterface $program = null)
    {
        return $program && $this->userHasPermission($user, self::CAN_WRITE, 'program', $program->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function userHasWritePermissionToSchool(UserInterface $user, $schoolId = null)
    {
        return $schoolId && $this->userHasPermission($user, self::CAN_WRITE, 'school', $schoolId);
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function userHasReadPermissionToCoursesAssociatedWithCohort(
        UserInterface $user,
        CohortInterface $cohort = null
    ) {
        if (! $cohort) {
            return false;
        }

        foreach ($cohort->getCourses() as $course) {
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
            'tableName'     => 'school',
            $permission => true,
            'user'          => $user,
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
