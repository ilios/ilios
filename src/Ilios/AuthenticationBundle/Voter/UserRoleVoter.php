<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\UserRoleInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class UserRoleVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class UserRoleVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\UserRoleInterface');
    }

    /**
     * @param string $attribute
     * @param UserRoleInterface $role
     * @param UserInterface $user
     * @return bool
     */
    protected function isGranted($attribute, $role, $user = null)
    {
        if (!$user instanceof UserInterface) {
            return false;
        }

        // all authenticated users can view user roles,
        // but only developers can create/modify/delete them directly.
        switch ($attribute) {
            case self::VIEW:
                return true;
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                return $this->userHasRole($user, ['Developer']);
                break;
        }
        return false;
    }
}
