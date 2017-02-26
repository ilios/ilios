<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\PermissionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class PermissionVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class PermissionVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof PermissionInterface && in_array($attribute, array(
            self::VIEW, self::CREATE, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param PermissionInterface $permission
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $permission, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            // at least one of these must be true.
            // 1. the permission applies to the current user
            // 2. the current user has developer role
            case self::VIEW:
                return (
                    $user->isTheUser($permission->getUser())
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
