<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\RecurringEventInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class SessionVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class RecurringEventVoter extends SessionVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\RecurringEventInterface');
    }

    /**
     * @param string $attribute
     * @param RecurringEventInterface $event
     * @param UserInterface|null $user
     * @return bool
     */
    protected function isGranted($attribute, $event, $user = null)
    {
        $offerings = $event->getOfferings(); // nonsense, but currently necessary. see issue #929.
        if ($offerings->isEmpty()) { // orphaned event, don't grant access to it.
            return false;
        }

        // get the events parent offering, there should only be one.
        $offering = $event->getOfferings()->first();

        // grant perms based on the session that owns the parent offering
        return parent::isGranted($attribute, $offering->getSession(), $user);
    }
}
