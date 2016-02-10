<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\CoreBundle\Entity\DTO\CourseDTO;
use Ilios\CoreBundle\Entity\Manager\CourseManagerInterface;
use Ilios\CoreBundle\Entity\Manager\PermissionManagerInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Ilios\AuthenticationBundle\Voter\AbstractVoter;

/**
 * Class CourseDTOVoter
 * @package Ilios\AuthenticationBundle\Voter\ApiProxy\V1
 */
class CourseDTOVoter extends AbstractVoter
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
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof CourseDTO && in_array($attribute, array(
            self::CREATE, self::VIEW, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param CourseDTO $course
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $course, TokenInterface $token)
    {
        $user = $token->getUser();
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
     * @param CourseDTO $course
     * @param UserInterface $user
     * @return bool
     */
    protected function isViewGranted(CourseDTO $course, $user)
    {
        return true;
        // grant VIEW privileges if at least one of the following
        // statements is true:
        // 1. the user's primary school is the course's owning school
        // 2. the user is instructing ILMs or offerings in this course
        // 3. the user is directing this course
        // 4. the user has READ rights on the course's owning school via the permissions system
        // 5. the user has READ rights on the course via the permissions system
        return (
            ($course->school === $user->getSchool()->getId())
            || $this->courseManager->isUserInstructingInCourse($user, $course->id)
            || $user->getDirectedCourses()->contains($course)
            || $this->permissionManager->userHasReadPermissionToSchool($user, $course->school)
            || $this->permissionManager->userHasReadPermissionToCourse($user, $course->id)
        );
    }

    /**
     * @param CourseDTO $course
     * @param UserInterface $user
     * @return bool
     */
    protected function isWriteGranted(CourseDTO $course, $user)
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
            && ($this->schoolsAreIdentical($course->school, $user->getSchool())
                || $this->permissionManager->userHasWritePermissionToSchool($user, $course->school)
            )
            || $this->permissionManager->userHasWritePermissionToCourse($user, $course->id)
        );
    }
}
