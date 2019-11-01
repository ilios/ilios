<?php

namespace App\RelationshipVoter;

use App\Classes\SchoolEvent as Event;
use App\Classes\SessionUserInterface;
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


        // if the event is published and the it's owned by the current user's
        // primary school, then it can be viewed.
        if ($event->isPublished && $user->getSchoolId() === $event->school) {
            return true;
        }

        $sessionId = $event->session;
        $courseId = $event->course;
        $schoolId = $event->school;
        $offeringId = $event->offering;
        $ilmId = $event->ilmSession;

        // if the current user is associated with the given event
        // in a directing/administrating/instructing capacity via the event's
        // owning school/course/session/ILM/offering context,
        // then it can be viewed.
        if (in_array($schoolId, $user->getAdministeredSchoolIds())
            || in_array($schoolId, $user->getDirectedSchoolIds())
            || in_array($schoolId, $user->getDirectedProgramSchoolIds())
            || in_array($courseId, $user->getAdministeredCourseIds())
            || in_array($courseId, $user->getDirectedCourseIds())
            || in_array($sessionId, $user->getAdministeredSessionIds())
            || in_array($offeringId, $user->getInstructedOfferingIds())
            || in_array($ilmId, $user->getInstructedIlmIds())
        ) {
            return true;
        }

        return false;
    }
}
