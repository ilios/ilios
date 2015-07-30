<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\AlertChangeTypeInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class AlertChangeTypeVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class AlertChangeTypeVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\AlertChangeTypeInterface');
    }

    /**
     * @param string $attribute
     * @param AlertChangeTypeInterface $type
     * @param UserInterface $user
     * @return bool
     */
    protected function isGranted($attribute, $type, $user = null)
    {
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return true;
                break;
            case self::EDIT:
            case self::DELETE:
                return $this->userHasRole($user, ['Developer']);
                break;
        }

        return false;
    }
}
