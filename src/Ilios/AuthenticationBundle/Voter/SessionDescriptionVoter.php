<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\SessionDescriptionInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class SessionVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class SessionDescriptionVoter extends SessionVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\SessionDescriptionInterface');
    }

    /**
     * @param string $attribute
     * @param SessionDescriptionInterface $description
     * @param UserInterface|null $user
     * @return bool
     */
    protected function isGranted($attribute, $description, $user = null)
    {
        $session = $description->getSession();
        if (! $session) {
            return false;
        }
        // grant perms based on the owning session
        return parent::isGranted($attribute, $session, $user);
    }
}
