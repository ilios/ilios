<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Traits\LockableEntityInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class LockableVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class LockableVoter extends Voter
{
    /**
     * @var string
     */
    const MODIFY = 'modify';

    /**
     * @var string
     */
    const UNLOCK = 'unlock';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof LockableEntityInterface && in_array($attribute, [self::MODIFY, self::UNLOCK]);
    }

    /**
     * @param string $attribute
     * @param LockableEntityInterface $lockable
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $lockable, TokenInterface $token)
    {
        if (self::UNLOCK === $attribute) {
            $user = $token->getUser();
            if (!$user instanceof UserInterface) {
                return false;
            }

            return $user->hasRole(['Developer']);
        }

        if (self::MODIFY === $attribute) {
            return ! $lockable->isLocked();
        }
        return false;
    }
}
