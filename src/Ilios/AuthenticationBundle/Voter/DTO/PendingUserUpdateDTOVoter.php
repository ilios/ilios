<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\CoreBundle\Entity\DTO\PendingUserUpdateDTO;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class PendingUserUpdateDTOVoter
 */
class PendingUserUpdateDTOVoter extends AbstractVoter
{
    /**
     * @inheritdoc
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof PendingUserUpdateDTO && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param PendingUserUpdateDTO $requestedPendingUserUpdate
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $requestedPendingUserUpdate, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return (
                    $user->getId() === $requestedPendingUserUpdate->user
                    || $user->hasRole(['Course Director', 'Faculty', 'Developer'])
                );
                break;
        }

        return false;
    }
}
