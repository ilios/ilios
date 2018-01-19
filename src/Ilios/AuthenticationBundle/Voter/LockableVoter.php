<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Traits\LockableEntityInterface;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class LockableVoter
 */
class LockableVoter extends AbstractVoter
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
        if ($this->abstain) {
            return false;
        }

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
            if (!$user instanceof SessionUserInterface) {
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
