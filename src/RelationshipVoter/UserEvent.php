<?php

namespace App\RelationshipVoter;

use App\Classes\UserEvent as Event;
use App\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class UserEvent
 */
class UserEvent extends AbstractCalendarEvent
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof Event && in_array($attribute, array(self::VIEW, self::VIEW_DRAFT_CONTENTS));
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

        switch ($attribute) {
            case self::VIEW:
                // if the event is published and owned by the current user
                // then it can be viewed.
                if ($event->isPublished && $user->getId() === $event->user) {
                    return true;
                }

                // if the current user is associated with the given event
                // in a directing/administrating/instructing capacity via the event's
                // owning school/course/session/ILM/offering context,
                // then it can be viewed, even if it is not published.
                return $this->isUserAdministratorDirectorsOrInstructorOfEvent($user, $event);

            case self::VIEW_DRAFT_CONTENTS:
                // can't view draft data on other user's event
                if ($user->getId() !== $event->user) {
                    return false;
                }
                // can't view draft data on events owned by the current user, unless
                // the event is being instructed/directed/administered by the current user.
                return $this->isUserAdministratorDirectorsOrInstructorOfEvent($user, $event);

            default:
                return false;
        }
    }
}
