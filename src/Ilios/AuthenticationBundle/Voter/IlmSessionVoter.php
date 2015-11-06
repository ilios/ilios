<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\IlmSessionInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class IlmSessionVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class IlmSessionVoter extends SessionVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\IlmSessionInterface');
    }

    /**
     * @param string $attribute
     * @param IlmSessionInterface $ilmFacet
     * @param UserInterface $user
     * @return bool
     */
    protected function isGranted($attribute, $ilmFacet, $user = null)
    {
        // grant perms based on the session
        $session = $ilmFacet->getSession();
        if (! $session) {
            return false;
        }
        return parent::isGranted($attribute, $session, $user);

    }
}
