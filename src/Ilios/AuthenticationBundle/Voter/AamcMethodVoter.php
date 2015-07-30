<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\AamcMethod;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class AamcMethodVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class AamcMethodVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\AamcMethod');
    }

    /**
     * @param string $attribute
     * @param AamcMethod $aamcMethod
     * @param UserInterface $user
     * @return array|bool
     */
    protected function isGranted($attribute, $aamcMethod, $user = null)
    {
        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
            case self::EDIT:
            case self::DELETE:
                return $this->userHasRole($user, ['Course Director', 'Developer']);
                break;
        }

        return false;
    }
}
