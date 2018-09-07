<?php

namespace AppBundle\RelationshipVoter;

use AppBundle\Classes\SchoolEvent as Event;
use AppBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class SchoolEvent
 */
class SchoolEvent extends AbstractVoter
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

        if ($user->isRoot()) {
            return true;
        }


        // if the current user performs any non-learner functions,
        // then check if the event's school matches the current user's primary school,
        // or any of the associated schools in a non-learner context.
        // if so, grant VIEW access.
        if ($user->performsNonLearnerFunction()) {
            $schoolIds = $user->getAssociatedSchoolIdsInNonLearnerFunction();
            return $user->getSchoolId() === $event->school || in_array($event->school, $schoolIds);
        }

        // student/learners can only VIEW published events in their primary school.
        // @todo perhaps this is to restrictive, needs review [ST 2018/01/17]
        return $event->isPublished && $user->getSchoolId() === $event->school;
    }
}
