<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\CoreBundle\Entity\IngestionException;

/**
 * Class SchoolVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class IngestionExceptionVoter extends AbstractVoter
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
        return array('Ilios\CoreBundle\Entity\IngestionException');
    }

    /**
     * @param string $attribute
     * @param IngestionException $exception
     * @param UserInterface|null $user
     * @return bool
     */
    protected function isGranted($attribute, $exception, $user = null)
    {
        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                // Grant VIEW access only to users with the Developer role.
                return $this->userHasRole($user, ['Developer']);
                break;
        }

        return false;
    }
}
