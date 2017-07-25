<?php

namespace Ilios\AuthenticationBundle\Voter\Entity;

use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\CoreBundle\Entity\ApplicationConfigInterface;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class ApplicationConfigVoter
 */
class ApplicationConfigEntityVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof ApplicationConfigInterface && in_array($attribute, array(
                self::CREATE, self::VIEW, self::EDIT, self::DELETE
            ));
    }

    /**
     * @param string $attribute
     * @param ApplicationConfigInterface $applicationConfig
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $applicationConfig, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        // only grant VIEW, CREATE, EDIT and DELETE privileges
        // if the user has the 'Developer' role
        return $user->hasRole(['Developer']);
    }
}
