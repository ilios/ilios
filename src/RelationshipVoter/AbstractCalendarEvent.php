<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\CalendarEvent;
use App\Classes\SessionUserInterface;

abstract class AbstractCalendarEvent extends AbstractVoter
{
    /**
     * Checks if the given user is associated with the given event
     * in a directing/administrating/instructing capacity via the event's
     * owning school/course/session/ILM/offering context,
     */
    protected function isUserAdministratorDirectorsOrInstructorOfEvent(
        SessionUserInterface $user,
        CalendarEvent $event
    ): bool {
        $sessionId = $event->session;
        $courseId = $event->course;
        $schoolId = $event->school;
        $offeringId = $event->offering;
        $ilmId = $event->ilmSession;

        return $user->isAdministeringSchool($schoolId)
            || $user->isDirectingSchool($schoolId)
            || $user->isDirectingProgramLinkedToCourse($schoolId)
            || $user->isAdministeringCourse($courseId)
            || $user->isDirectingCourse($courseId)
            || $user->isAdministeringSession($sessionId)
            || ($offeringId && $user->isInstructingOffering($offeringId))
            || ($ilmId && $user->isInstructingIlm($ilmId));
    }
}
