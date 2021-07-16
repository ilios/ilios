<?php

declare(strict_types=1);

namespace App\Classes;

use App\Attribute as IA;
use DateTime;

#[IA\DTO('userEvent')]
class UserEvent extends CalendarEvent
{
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $user;

    public static function createFromCalendarEvent(int $userId, CalendarEvent $event): UserEvent
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
