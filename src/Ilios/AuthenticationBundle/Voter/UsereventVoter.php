<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class SchoolVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class UsereventVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedAttributes()
    {
        return array(self::VIEW);
    }

    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\UserInterface');
    }

    /**
     * @param string $attribute
     * @param UserInterface $requestedUser
     * @param UserInterface|null $user
     * @return bool
     */
    protected function isGranted($attribute, $requestedUser, $user = null)
    {
        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                // check if requested user is current user
                return $requestedUser->getId() === $user->getId();
                break;
        }

        return false;
    }
}
