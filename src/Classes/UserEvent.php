<?php

namespace App\Classes;

use App\Annotation as IS;

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
     * @param CalendarEvent $event
     * @return UserEvent
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
}
