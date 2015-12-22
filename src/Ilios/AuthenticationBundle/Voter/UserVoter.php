<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\Manager\PermissionManagerInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class UserVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class UserVoter extends AbstractVoter
{
    /**
     * @var PermissionManagerInterface
     */
    protected $permissionManager;

    /**
     * @param PermissionManagerInterface $permissionManager
     */
    public function __construct(PermissionManagerInterface $permissionManager)
    {
        $this->permissionManager = $permissionManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\UserInterface');
    }

    /**
     * @param string $attribute
     * @param UserInterface $requestedUser
     * @param UserInterface|null $user
     * @return bool
     */
    protected function isGranted($attribute, $requestedUser, $user = null)
    {
        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        switch ($attribute) {
            // at least one of these must be true.
            // 1. the requested user is the current user
            // 2. the current user has faculty/course director/developer role
            case self::VIEW:
                return (
                    $user->getId() === $requestedUser->getId()
                    || $this->userHasRole($user, ['Course Director', 'Faculty', 'Developer'])
                );
                break;
            // at least one of these must be true.
            // 1. the current user has developer role
            //    and has the same primary school affiliation as the given user
            // 2. the current user has developer role
            //    and has WRITE rights to one of the users affiliated schools.
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                return (
                    $this->userHasRole($user, ['Developer'])
                    && (
                        $requestedUser->getAllSchools()->contains($user->getSchool())
                        || $this->permissionManager->userHasReadPermissionToSchools(
                            $user,
                            $requestedUser->getAllSchools()
                        )
                    )
                );
                break;
        }
        return false;
    }
}
