<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\CoreBundle\Classes\UserEvent as Event;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class UserEvent
 */
class UserEvent extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof Event && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param Event $event
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $event, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        // root user can see all user events
        if ($user->isRoot()) {
            return true;
        }

        // if the current user performs any non-learner functions,
        // they can see their user events, regardless of published status.
        if ($user->performsNonLearnerFunction()) {
            return $user->getId() === $event->user;
        }

        // otherwise, only published user events owned by the current user are accessible.
        return $event->isPublished && $user->getId() === $event->user;
    }
}
