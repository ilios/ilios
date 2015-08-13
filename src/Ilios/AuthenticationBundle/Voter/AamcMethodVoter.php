<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\AamcMethodInterface;
use Ilios\CoreBundle\Entity\UserInterface;

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
        return array('Ilios\CoreBundle\Entity\AamcMethodInterface');
    }

    /**
     * @param string $attribute
     * @param AamcMethodInterface $aamcMethod
     * @param UserInterface $user
     * @return bool
     */
    protected function isGranted($attribute, $aamcMethod, $user = null)
    {
        if (!$user instanceof UserInterface) {
            return false;
        }

        // all authenticated users can view AAMC methods,
        // but only developers can create/modify/delete them directly.
        switch ($attribute) {
            case self::VIEW:
                return true;
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                return $this->userHasRole($user, ['Developer']);
                break;
        }

        return false;
    }
}
