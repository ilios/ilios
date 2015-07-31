<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\AlertInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class AlertVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class AlertVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\AlertInterface');
    }

    /**
     * @param string $attribute
     * @param AlertInterface $alert
     * @param UserInterface $user
     * @return bool
     * @todo revisit implementation. [ST 2015/07/31]
     */
    protected function isGranted($attribute, $alert, $user = null)
    {
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
            case self::EDIT:
            case self::DELETE:
                return $this->userHasRole($user, ['Developer']);
                break;
        }

        return false;
    }
}
