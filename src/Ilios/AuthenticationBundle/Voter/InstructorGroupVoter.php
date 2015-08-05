<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\InstructorGroupInterface;
use Ilios\CoreBundle\Entity\Manager\PermissionManagerInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class InstructorGroupVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class InstructorGroupVoter extends AbstractVoter
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
        return array('Ilios\CoreBundle\Entity\InstructorGroupInterface');
    }

    /**
     * @param string $attribute
     * @param InstructorGroupInterface $group
     * @param UserInterface|null $user
     * @return bool
     */
    protected function isGranted($attribute, $group, $user = null)
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
                //    and has at least one of 'Course Director', 'Faculty' and 'Developer' roles.
                // 2. the user has READ rights on the groups 's owning school via the permissions system
                //    and has at least one of 'InstructorGroup Director', 'Faculty' and 'Developer' roles.
                return ($this->userHasRole($user, ['Course Director', 'Faculty', 'Developer'])
                    && (
                        $user->getPrimarySchool() === $group->getSchool()
                        || $this->permissionManager->userHasReadPermissionToSchool($user, $group->getSchool())
                    )
                );
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                // grant CREATE, EDIT and DELETE privileges if at least one of the following
                // statements is true:
                // 1. the user's primary school is the course's owning school
                //    and the user has at least one of the 'Course Director' and 'Developer' roles.
                // 2. the user has WRITE rights on the course's owning school via the permissions system
                //    and the user has at least one of the 'Course Director' and 'Developer' roles.
                return ($this->userHasRole($user, ['Course Director', 'Developer'])
                && (
                    $user->getPrimarySchool() === $group->getSchool()
                    || $this->permissionManager->userHasWritePermissionToSchool($user, $group->getSchool())
                )
            );
                break;
        }
        return false;
    }
}
