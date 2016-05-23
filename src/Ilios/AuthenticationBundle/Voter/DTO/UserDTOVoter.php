<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\AuthenticationBundle\Voter\Entity\UserEntityVoter;
use Ilios\CoreBundle\Entity\DTO\UserDTO;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class UserDTOVoter
 * @package Ilios\AuthenticationBundle\Voter\DTO
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
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return (
                    $user->getId() === $requestedUser->id
                    || $this->userHasRole($user, ['Course Director', 'Faculty', 'Developer'])
                );
                break;
        }

        return false;
    }
}
