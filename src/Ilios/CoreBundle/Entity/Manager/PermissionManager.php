<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
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
    public function userHasReadPermissionToCourse(UserInterface $user, CourseInterface $course = null)
    {
        return $course && $this->userHasPermission($user, self::CAN_READ, 'course', $course->getId());
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
    public function userHasReadPermissionToSchool(UserInterface $user, SchoolInterface $school = null)
    {
        return $school && $this->userHasPermission($user, self::CAN_READ, 'school', $school->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function userHasReadPermissionToSchools(UserInterface $user, ArrayCollection $schools)
    {
        $criteria = [
            'tableName'     => 'school',
            self::CAN_READ  => true,
            'user'          => $user,
        ];

        $schoolsWithReadPermission = $this->findPermissionsBy($criteria);
        $overlap = array_intersect($schools->toArray(), $schoolsWithReadPermission);

        return ! empty($overlap);

    }

    /**
     * {@inheritdoc}
     */
    public function userHasWritePermissionToSchools(UserInterface $user, ArrayCollection $schools)
    {
        $criteria = [
            'tableName'     => 'school',
            self::CAN_WRITE => true,
            'user'          => $user,
        ];

        $schoolsWithWritePermission = $this->findPermissionsBy($criteria);
        $overlap = array_intersect($schools->toArray(), $schoolsWithWritePermission->toArray());

        return ! empty($overlap);
    }

    /**
     * {@inheritdoc}
     */
    public function userHasWritePermissionToCourse(UserInterface $user, CourseInterface $course = null)
    {
        return $course && $this->userHasPermission($user, self::CAN_WRITE, 'course', $course->getId());
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
    public function userHasWritePermissionToSchool(UserInterface $user, SchoolInterface $school = null)
    {
        return $school && $this->userHasPermission($user, self::CAN_WRITE, 'school', $school->getId());
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
}
