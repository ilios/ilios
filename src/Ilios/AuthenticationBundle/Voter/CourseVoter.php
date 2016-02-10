<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\CourseInterface;
use Ilios\CoreBundle\Entity\Manager\CourseManagerInterface;
use Ilios\CoreBundle\Entity\Manager\PermissionManagerInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class CourseVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
abstract class CourseVoter extends AbstractVoter
{
    /**
     * @var PermissionManagerInterface
     */
    protected $permissionManager;

    /**
     * @var CourseManagerInterface
     */
    protected $courseManager;

    /**
     * @param CourseManagerInterface $courseManager
     * @param PermissionManagerInterface $permissionManager
     */
    public function __construct(CourseManagerInterface $courseManager, PermissionManagerInterface $permissionManager)
    {
        $this->courseManager = $courseManager;
        $this->permissionManager = $permissionManager;
    }

    /**
     * @param int $courseId
     * @param int $owningSchoolId
     * @param UserInterface $user
     *
     * @return bool
     */
    protected function isViewGranted($courseId, $owningSchoolId, UserInterface $user)
    {
        // grant VIEW privileges if at least one of the following
        // statements is true:
        // 1. the user's primary school is the course's owning school
        // 2. the user is instructing ILMs or offerings in this course
        // 3. the user is directing this course
        // 4. the user has READ rights on the course's owning school via the permissions system
        // 5. the user has READ rights on the course via the permissions system
        return (
            $owningSchoolId === $user->getSchool()->getId()
            || $this->courseManager->isUserInstructingInCourse($user, $courseId)
            || $user->isDirectingCourse($courseId)
            || $this->permissionManager->userHasReadPermissionToSchool($user, $owningSchoolId)
            || $this->permissionManager->userHasReadPermissionToCourse($user, $courseId)
        );
    }

    /**
     * @param int $courseId
     * @param int $owningSchoolId
     * @param UserInterface $user
     *
     * @return bool
     */
    protected function isWriteGranted($courseId, $owningSchoolId, UserInterface $user)
    {
        // grant CREATE/EDIT/DELETE privileges if at least one of the following
        // statements is true:
        // 1. the user's primary school is the course's owning school
        //    and the user has at least one of the 'Faculty', 'Course Director' and 'Developer' roles.
        // 2. the user has WRITE rights on the course's owning school via the permissions system
        //    and the user has at least one of the 'Faculty', 'Course Director' and 'Developer' roles.
        // 3. the user has WRITE rights on the course via the permissions system
        return (
            $this->userHasRole($user, ['Faculty', 'Course Director', 'Developer'])
            && (
                $owningSchoolId === $user->getSchool()->getId()
                || $this->permissionManager->userHasWritePermissionToSchool($user, $owningSchoolId)
            )
            || $this->permissionManager->userHasWritePermissionToCourse($user, $courseId)
        );
    }
}
