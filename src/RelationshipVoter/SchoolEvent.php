<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\CalendarEvent;
use App\Classes\SchoolEvent as Event;
use App\Classes\SessionUserInterface;
use App\Classes\VoterPermissions;
use App\Service\SessionUserPermissionChecker;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class SchoolEvent
 */
class SchoolEvent extends AbstractCalendarEvent
{
    public function __construct(SessionUserPermissionChecker $permissionChecker)
    {
        parent::__construct(
            $permissionChecker,
            Event::class,
            [
                VoterPermissions::VIEW,
                VoterPermissions::VIEW_DRAFT_CONTENTS,
                VoterPermissions::VIEW_VIRTUAL_LINK,
            ]
        );
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        if ($user->isRoot()) {
            return true;
        }

        switch ($attribute) {
            case VoterPermissions::VIEW:
                // if the event is published, and it's owned by the current user's
                // primary school, then it can be viewed.
                if ($subject->isPublished && $user->getSchoolId() === $subject->school) {
                    return true;
                }

                // if the current user is associated with the given event
                // in a directing/administrating/instructing capacity via the event's
                // owning school/course/session/ILM/offering context,
                // then it can be viewed, even if it is not published.
                return $this->isUserAdministratorDirectorsOrInstructorOfEvent($user, $subject);

            case VoterPermissions::VIEW_DRAFT_CONTENTS:
                // can't view draft data on events, unless
                // the event is being instructed/directed/administered by the current user.
                return $this->isUserAdministratorDirectorsOrInstructorOfEvent($user, $subject);
            case VoterPermissions::VIEW_VIRTUAL_LINK:
                return $this->isUserAdministratorDirectorsOrInstructorOfEvent($user, $subject)
                    || $this->isUserLearnerInEvent($user, $subject);
            default:
                return false;
        }
    }

    protected function isUserLearnerInEvent(SessionUserInterface $user, Event $event): bool
    {
        $offeringId = $event->offering;
        $ilmId = $event->ilmSession;

        return ($ilmId && $user->isLearnerInIlm($ilmId))
            || ($offeringId && $user->isLearnerInOffering($offeringId));
    }
}
