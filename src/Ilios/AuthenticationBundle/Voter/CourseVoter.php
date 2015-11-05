<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\CourseInterface;
use Ilios\CoreBundle\Entity\Manager\CourseManagerInterface;
use Ilios\CoreBundle\Entity\Manager\PermissionManagerInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class CourseVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class CourseVoter extends AbstractVoter
{
    /**
     * @var PermissionManagerInterface
     */
    protected $permissionManager;

    /**
     * @var CourseManagerInterface
     */
    protected $courseManager;

    public function __construct(CourseManagerInterface $courseManager, PermissionManagerInterface $permissionManager)
    {
        $this->courseManager = $courseManager;
        $this->permissionManager = $permissionManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\CourseInterface');
    }

    /**
     * @param string $attribute
     * @param CourseInterface $course
     * @param UserInterface|null $user
     * @return bool
     */
    protected function isGranted($attribute, $course, $user = null)
    {
        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return $this->isViewGranted($course, $user);
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                return $this->isWriteGranted($course, $user);
                break;
        }
        return false;
    }

    /**
     * @param CourseInterface $course
     * @param UserInterface $user
     * @return bool
     */
    protected function isViewGranted($course, $user)
    {
        // grant VIEW privileges if at least one of the following
        // statements is true:
        // 1. the user's primary school is the course's owning school
        // 2. the user is instructing ILMs or offerings in this course
        // 3. the user is directing this course
        // 4. the user has READ rights on the course's owning school via the permissions system
        // 5. the user has READ rights on the course via the permissions system
        return (
            $this->schoolsAreIdentical($course->getSchool(), $user->getSchool())
            || $this->courseManager->isUserInstructingInCourse($user, $course)
            || $user->getDirectedCourses()->contains($course)
            || $this->permissionManager->userHasReadPermissionToSchool($user, $course->getSchool())
            || $this->permissionManager->userHasReadPermissionToCourse($user, $course)
        );
    }

    /**
     * @param CourseInterface $course
     * @param UserInterface $user
     * @return bool
     */
    protected function isWriteGranted($course, $user)
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
            && ($this->schoolsAreIdentical($course->getSchool(), $user->getSchool())
                || $this->permissionManager->userHasWritePermissionToSchool($user, $course->getSchool())
            )
            || $this->permissionManager->userHasWritePermissionToCourse($user, $course)
        );
    }
}
