<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\UserEvent as Event;
use App\Classes\SessionUserInterface;
use App\Classes\VoterPermissions;
use App\Service\SessionUserPermissionChecker;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserEvent extends AbstractCalendarEvent
{
    public function __construct(SessionUserPermissionChecker $permissionChecker)
    {
        parent::__construct(
            $permissionChecker,
            Event::class,
            [
                VoterPermissions::VIEW,
                VoterPermissions::VIEW_DRAFT_CONTENTS,
            ]
        );
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
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
            case VoterPermissions::VIEW:
                // if the event is published and owned by the current user
                // then it can be viewed.
                if ($subject->isPublished && $user->getId() === $subject->user) {
                    return true;
                }

                // if the current user is associated with the given event
                // in a directing/administrating/instructing capacity via the event's
                // owning school/course/session/ILM/offering context,
                // then it can be viewed, even if it is not published.
                return $this->isUserAdministratorDirectorsOrInstructorOfEvent($user, $subject);

            case VoterPermissions::VIEW_DRAFT_CONTENTS:
                // can't view draft data on events owned by the current user, unless
                // the event is being instructed/directed/administered by the current user.
                return $this->isUserAdministratorDirectorsOrInstructorOfEvent($user, $subject);

            default:
                return false;
        }
    }
}
