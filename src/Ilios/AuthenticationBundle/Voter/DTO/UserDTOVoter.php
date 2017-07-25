<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\AuthenticationBundle\Voter\Entity\UserEntityVoter;
use Ilios\CoreBundle\Entity\DTO\UserDTO;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class UserDTOVoter
 */
class UserDTOVoter extends UserEntityVoter
{
    /**
     * @inheritdoc
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof UserDTO && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param UserDTO $requestedUser
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $requestedUser, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return (
                    $user->getId() === $requestedUser->id
                    || $user->hasRole(['Course Director', 'Faculty', 'Developer'])
                );
                break;
        }

        return false;
    }
}
