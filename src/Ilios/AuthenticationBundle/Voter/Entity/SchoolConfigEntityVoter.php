<?php

namespace Ilios\AuthenticationBundle\Voter\Entity;

use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\CoreBundle\Entity\SchoolConfigInterface;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class SchoolConfigEntityVoter
 */
class SchoolConfigEntityVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof SchoolConfigInterface && in_array($attribute, array(
                self::CREATE, self::VIEW, self::EDIT, self::DELETE
            ));
    }

    /**
     * @param string $attribute
     * @param SchoolConfigInterface $schoolConfig
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $schoolConfig, TokenInterface $token)
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
                return $user->hasRole(['Developer']);
                break;
        }

        return false;
    }
}
