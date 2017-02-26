<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\ApplicationConfigInterface;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class ApplicationConfigVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class ApplicationConfigVoter extends AbstractVoter
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

        switch ($attribute) {
            // grant VIEW privileges to all authenticated users.
            case self::VIEW:
                return true;
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                // grant CREATE, EDIT and DELETE privileges
                // if the user has the 'Developer' role
                return $this->userHasRole($user, ['Developer']);
                break;
        }

        return false;
    }
}
