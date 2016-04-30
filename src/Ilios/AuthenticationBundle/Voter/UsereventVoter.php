<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\Manager\UserManagerInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\CoreBundle\Classes\UserEvent;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class SchoolVoter
 * @package Ilios\AuthenticationBundle\Voter
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
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                // users with developer roles can see their own events, regardless of publication status.
                // and any other published events that are no theirs.
                if ($this->userHasRole($user, ['Developer'])) {
                    return ($user->getId() === $event->user || $event->isPublished);
                }

                // faculty and course directors can see their own events, regardless of publication status.
                if ($this->userHasRole($user, ['Faculty', 'Course Director'])) {
                    return $user->getId() === $event->user;
                }

                // everyone else gets to see their own, published events.
                return ($user->getId() === $event->user && $event->isPublished);
        }
        return false;
    }
}
