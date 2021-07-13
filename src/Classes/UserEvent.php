<?php

declare(strict_types=1);

namespace App\Classes;

use App\Annotation as IS;
use DateTime;

/**
 * Class UserEvent
 *
 * @IS\DTO
 */
class UserEvent extends CalendarEvent
{
    /**
     * @var int
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $user;

    /**
     * Creates a new user event from a given user id and a given calendar event.
     * @param int $userId
     */
    public static function createFromCalendarEvent($userId, CalendarEvent $event): UserEvent
    {
        $userEvent = new UserEvent();
        $userEvent->user = $userId;
        foreach (get_object_vars($event) as $key => $name) {
            $userEvent->$key = $name;
        }
        return $userEvent;
    }

    /**
     * This information is not available to un-privileged users
     */
    public function clearDataForUnprivilegedUsers(DateTime $dateTime)
    {
        $this->instructionalNotes = null;
        $this->clearDataForDraftOrScheduledEvent();
        $this->removeMaterialsInDraft();
        $this->clearTimedMaterials($dateTime);
    }

    protected function clearTimedMaterials(DateTime $dateTime)
    {
        /** @var UserMaterial $lm */
        foreach ($this->learningMaterials as $lm) {
            $lm->clearTimedMaterial($dateTime);
        }
    }
}
