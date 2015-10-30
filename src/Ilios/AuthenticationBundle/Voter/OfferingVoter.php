<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\OfferingInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class SessionVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class OfferingVoter extends SessionVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\OfferingInterface');
    }

    /**
     * @param string $attribute
     * @param OfferingInterface $offering
     * @param UserInterface|null $user
     * @return bool
     */
    protected function isGranted($attribute, $offering, $user = null)
    {
        $session = $offering->getSession();
        if (! $session) {
            return false;
        }
        // grant perms based on the owning session
        return parent::isGranted($attribute, $session, $user);
    }
}
