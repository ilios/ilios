<?php

namespace Ilios\AuthenticationBundle\Voter\Entity;

use Ilios\CoreBundle\Entity\SessionDescriptionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class SessionDescriptionEntityVoter
 */
class SessionDescriptionEntityVoter extends SessionEntityVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof SessionDescriptionInterface && in_array($attribute, array(
            self::VIEW, self::CREATE, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param SessionDescriptionInterface $description
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $description, TokenInterface $token)
    {
        $session = $description->getSession();
        if (! $session) {
            return false;
        }
        // grant perms based on the owning session
        return parent::voteOnAttribute($attribute, $session, $token);
    }
}
