<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\AuthenticationBundle\Voter\Entity\SessionEntityVoter;
use Ilios\CoreBundle\Entity\IlmSessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class IlmSessionVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class IlmSessionVoter extends SessionEntityVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof IlmSessionInterface && in_array($attribute, array(
            self::VIEW, self::CREATE, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param IlmSessionInterface $ilmFacet
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $ilmFacet, TokenInterface $token)
    {
        // grant perms based on the session
        $session = $ilmFacet->getSession();
        if (! $session) {
            return false;
        }
        return parent::voteOnAttribute($attribute, $session, $token);
    }
}
