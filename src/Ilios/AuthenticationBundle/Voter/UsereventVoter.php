<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Classes\UserEvent;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class SchoolVoter
 */
class UsereventVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof UserEvent && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param UserEvent $event
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $event, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                // users with developer roles can see their own events, regardless of publication status.
                // and any other published events that are not theirs.
                if ($user->hasRole(['Developer'])) {
                    return ($user->getId() === $event->user || $event->isPublished);
                }

                // faculty and course directors can see their own events, regardless of publication status.
                if ($user->hasRole(['Faculty', 'Course Director'])) {
                    return $user->getId() === $event->user;
                }

                // everyone else gets to see their own, published events.
                return ($user->getId() === $event->user && $event->isPublished);
        }
        return false;
    }
}
