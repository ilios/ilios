<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\PermissionInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class PermissionVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class PermissionVoter extends AbstractVoter
{

    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\PermissionInterface');
    }

    /**
     * @param string $attribute
     * @param PermissionInterface $permission
     * @param UserInterface|null $user
     * @return bool
     */
    protected function isGranted($attribute, $permission, $user = null)
    {
        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            // at least one of these must be true.
            // 1. the permission applies to the current user
            // 2. the current user has developer role
            case self::VIEW:
                return (
                    $this->usersAreIdentical($user, $permission->getUser())
                    || $this->userHasRole($user, ['Developer'])
                );
                break;
            // the current user must have 'developer' role in order to create, update or delete permissions.
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                return $this->userHasRole($user, ['Developer']);
                break;
        }
        return false;
    }
}
