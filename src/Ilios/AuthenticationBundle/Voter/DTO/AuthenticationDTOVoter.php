<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\CoreBundle\Entity\DTO\AuthenticationDTO;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class AuthenticationVoter
 */
class AuthenticationDTOVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof AuthenticationDTO && in_array($attribute, array(
            self::VIEW
        ));
    }

    /**
     * @param string $attribute
     * @param AuthenticationDTO $authentication
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $authenticationDTO, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            // at least one of these must be true.
            // 1. the requested authentication belongs to the current user
            // 2. the current user has faculty/course director/developer role
            case self::VIEW:
                return (
                    $user->getId() === $authenticationDTO->user
                    || $user->hasRole(['Developer'])
                );
                break;
        }

        return false;
    }
}
