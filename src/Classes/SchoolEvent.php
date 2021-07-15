<?php

declare(strict_types=1);

namespace App\Classes;

use App\Attribute as IA;
use DateTime;

/**
 * Class SchoolEvent
 */
#[IA\DTO]
class SchoolEvent extends CalendarEvent
{
    /**
     * Creates a new school event from a given school id and a given calendar event.
     * @param int $schoolId
     */
    public static function createFromCalendarEvent(CalendarEvent $event): SchoolEvent
    {
        $schoolEvent = new SchoolEvent();
        foreach (get_object_vars($event) as $key => $name) {
            $schoolEvent->$key = $name;
        }
        return $schoolEvent;
    }
    /**
     * Clear out all draft and schedule events as well as all materials
     */
    public function clearDataForUnprivilegedUsers()
    {
        $this->instructionalNotes = null;
        $this->clearDataForDraftOrScheduledEvent();
        $this->removeMaterialsInDraft();
        array_walk($this->learningMaterials, function (UserMaterial $lm) {
            $lm->clearMaterial();
        });
    }
    /**
     * Clear out all draft and schedule events as well as LMs based on time
     */
    public function clearDataForStudentAssociatedWithEvent(DateTime $dateTime)
    {
        $this->instructionalNotes = null;
        $this->clearDataForDraftOrScheduledEvent();
        $this->removeMaterialsInDraft();
        array_walk($this->learningMaterials, function (UserMaterial $lm) use ($dateTime) {
            $lm->clearTimedMaterial($dateTime);
        });
    }
}
