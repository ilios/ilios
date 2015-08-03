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
     * @param array $criteria
     * @param array $orderBy
     *
     * @return PermissionInterface
     */
    public function findPermissionBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|PermissionInterface[]
     */
    public function findPermissionsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param PermissionInterface $permission
     * @param bool $andFlush
     * @param bool $forceId
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
     * @param PermissionInterface $permission
     */
    public function deletePermission(
        PermissionInterface $permission
    ) {
        $this->em->remove($permission);
        $this->em->flush();
    }

    /**
     * @return PermissionInterface
     */
    public function createPermission()
    {
        $class = $this->getClass();

        return new $class();
    }

    /**
     * @param UserInterface $user
     * @param CourseInterface $course
     * @return bool
     */
    public function userHasReadPermissionsToCourse(UserInterface $user, CourseInterface $course)
    {
        return $this->userHasPermission($user, self::CAN_READ, 'course', $course->getId());
    }

    /**
     * @param UserInterface $user
     * @param ProgramInterface $program
     * @return bool
     */
    public function userHasReadPermissionsToProgram(UserInterface $user, ProgramInterface $program)
    {
        return $this->userHasPermission($user, self::CAN_READ, 'program', $program->getId());
    }

    /**
     * @param UserInterface $user
     * @param SchoolInterface $school
     * @return bool
     */
    public function userHasReadPermissionToSchool(UserInterface $user, SchoolInterface $school)
    {
        return $this->userHasPermission($user, self::CAN_READ, 'school', $school->getId());
    }

    /**
     * @param UserInterface $user
     * @param CourseInterface $course
     * @return bool
     */
    public function userHasWritePermissionsToCourse(UserInterface $user, CourseInterface $course)
    {
        return $this->userHasPermission($user, self::CAN_WRITE, 'course', $course->getId());
    }

    /**
     * @param UserInterface $user
     * @param ProgramInterface $program
     * @return bool
     */
    public function userHasWritePermissionsToProgram(UserInterface $user, ProgramInterface $program)
    {
        return $this->userHasPermission($user, self::CAN_WRITE, 'program', $program->getId());
    }

    /**
     * @param UserInterface $user
     * @param SchoolInterface $school
     * @return bool
     */
    public function userHasWritePermissionToSchool(UserInterface $user, SchoolInterface $school)
    {
        return $this->userHasPermission($user, self::CAN_WRITE, 'school', $school->getId());
    }

    /**
     * @param UserInterface $user
     * @param string $permission
     * @param $tableName
     * @param $tableRowId
     * @return bool
     */
    protected function userHasPermission(UserInterface $user, $permission = self::CAN_READ, $tableName, $tableRowId)
    {
        $criteria = [
            'tableRowId' => $tableRowId,
            'tableName' => $tableName,
            $permission => true,
            'user' => $user,
        ];

        $permission = $this->findPermissionBy($criteria);
        return empty($permission);
    }
}
