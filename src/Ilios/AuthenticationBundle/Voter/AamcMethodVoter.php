<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\AamcMethodInterface;
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
     * @param AamcMethodInterface $aamcMethod
     * @param UserInterface $user
     * @return array|bool
     */
    protected function isGranted($attribute, $aamcMethod, $user = null)
    {
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
