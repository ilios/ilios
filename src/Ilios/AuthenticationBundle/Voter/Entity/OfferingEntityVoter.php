<?php

namespace Ilios\AuthenticationBundle\Voter\Entity;

use Ilios\CoreBundle\Entity\OfferingInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class OfferingEntityVoter
 */
class OfferingEntityVoter extends SessionEntityVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof OfferingInterface && in_array($attribute, array(
            self::VIEW, self::CREATE, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param OfferingInterface $offering
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $offering, TokenInterface $token)
    {
        $session = $offering->getSession();
        if (! $session) {
            return false;
        }
        // grant perms based on the owning session
        return parent::voteOnAttribute($attribute, $session, $token);
    }
}
