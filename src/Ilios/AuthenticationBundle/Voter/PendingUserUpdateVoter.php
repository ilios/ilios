<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\PendingUserUpdateInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class PendingUserUpdateVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class PendingUserUpdateVoter extends UserVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof PendingUserUpdateInterface && in_array($attribute, array(
            self::VIEW, self::CREATE, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param PendingUserUpdateInterface $pendingUserUpdate
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $pendingUserUpdate, TokenInterface $token)
    {
        // grant perms based on the user
        return parent::voteOnAttribute($attribute, $pendingUserUpdate->getUser(), $token);
    }
}
