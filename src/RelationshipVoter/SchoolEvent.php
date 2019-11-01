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
        // then it can be viewed, even if it is not published.
        if ($user->isAdministeringSchool($schoolId)
            || $user->isDirectingSchool($schoolId)
            || $user->isDirectingProgramInSchool($schoolId)
            || $user->isAdministeringCourse($courseId)
            || $user->isDirectingCourse($courseId)
            || $user->isAdministeringSession($sessionId)
            || ($offeringId && $user->isInstructingOffering($offeringId))
            || ($ilmId && $user->isInstructingIlm($ilmId))
        ) {
            return true;
        }

        return false;
    }
}
