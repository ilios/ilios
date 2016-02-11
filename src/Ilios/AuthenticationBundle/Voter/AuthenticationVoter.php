<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\AuthenticationInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class AuthenticationVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class AuthenticationVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof AuthenticationInterface && in_array($attribute, array(
            self::CREATE, self::EDIT
        ));
    }

    /**
     * @param string $attribute
     * @param AuthenticationInterface $authentication
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $authentication, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::CREATE:
            case self::EDIT:
                return $this->userHasRole($user, ['Developer']);
                break;
        }

        return false;
    }
}
