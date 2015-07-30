<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\AamcPcrsInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class AamcPcrsVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class AamcPcrsVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\AamcPcrs');
    }

    /**
     * @param string $attribute
     * @param AamcPcrsInterface $aamcPcrs
     * @param UserInterface $user
     * @return bool
     */
    protected function isGranted($attribute, $aamcPcrs, $user = null)
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
