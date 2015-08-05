<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\CourseInterface;
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

    public function __construct(PermissionManagerInterface $permissionManager)
    {
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
                // grant VIEW privileges if at least one of the following
                // statements is true:
                // 1. the user's primary school is the course's owning school
                // 2. the user has READ rights on the course's owning school via the permissions system
                // 3. the user has READ rights on the course via the permissions system
                return ($course->getOwningSchool()->getId() === $user->getPrimarySchool()->getId()
                    || $this->permissionManager->userHasReadPermissionToSchool($user, $course->getOwningSchool())
                    || $this->permissionManager->userHasReadPermissionToCourse($user, $course)
                );
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                // grant CREATE, EDIT and DELETE privileges if at least one of the following
                // statements is true:
                // 1. the user's primary school is the course's owning school
                //    and the user has at least one of the 'Faculty', 'Course Director' and 'Developer' roles.
                // 2. the user has WRITE rights on the course's owning school via the permissions system
                //    and the user has at least one of the 'Faculty', 'Course Director' and 'Developer' roles.
                // 3. the user has WRITE rights on the course via the permissions system
                //
                // TODO
                // Figure out if we need to check if the user has been assigned as 'director'
                // to the given course, or is this implied by rule 2.
                // [ST 2015/08/03]
                return ($course->getOwningSchool()->getId() === $user->getPrimarySchool()->getId()
                    || $this->permissionManager->userHasWritePermissionToSchool($user, $course->getOwningSchool())
                    || $this->permissionManager->userHasWritePermissionToCourse($user, $course)
                );
                break;
        }

        return false;
    }
}
