<?php

declare(strict_types=1);

namespace App\Classes;

use App\Attributes as IA;
use DateTime;
use OpenApi\Attributes as OA;

#[IA\DTO('userEvent')]
#[OA\Schema(
    title: "UserEvent",
    properties: [
        new OA\Property(
            "name",
            description: "Name",
            type: "string"
        ),
        new OA\Property(
            "courseTitle",
            description: "Course title",
            type: "string"
        ),
        new OA\Property(
            "startDate",
            description: "Start date",
            type: "string",
            format: "date-time"
        ),
        new OA\Property(
            "endDate",
            description: "End date",
            type: "string",
            format: "date-time"
        ),
        new OA\Property(
            "offering",
            description: "Offering",
            type: "integer"
        ),
        new OA\Property(
            "ilmSession",
            description: "ILM session",
            type: "integer"
        ),
        new OA\Property(
            "color",
            description: "Color",
            type: "string"
        ),
        new OA\Property(
            "location",
            description: "Location",
            type: "string"
        ),
        new OA\Property(
            "url",
            description: "Virtual learning link",
            type: "string"
        ),
        new OA\Property(
            "lastModified",
            description: "The last time the details for this event were updated",
            type: "string",
            format: "date-time"
        ),
        new OA\Property(
            "isPublished",
            description: "Is published",
            type: "boolean"
        ),
        new OA\Property(
            "isScheduled",
            description: "Is partially published",
            type: "boolean"
        ),
        new OA\Property(
            "instructors",
            description: "Instructor names",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "learningMaterials",
            description: "Learning materials",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "attireRequired",
            description: "Is special attire required",
            type: "boolean"
        ),
        new OA\Property(
            "equipmentRequired",
            description: "Is special equipment required",
            type: "boolean"
        ),
        new OA\Property(
            "supplemental",
            description: "Is supplemental",
            type: "boolean"
        ),
        new OA\Property(
            "attendanceRequired",
            description: "Is attendance required",
            type: "boolean"
        ),
        new OA\Property(
            "session",
            description: "Session",
            type: "integer"
        ),
        new OA\Property(
            "course",
            description: "Course",
            type: "integer"
        ),
        new OA\Property(
            "courseExternalId",
            description: "Course external ID",
            type: "string"
        ),
        new OA\Property(
            "sessionTitle",
            description: "Session title",
            type: "string"
        ),
        new OA\Property(
            "sessionDescription",
            description: "Session description",
            type: "string"
        ),
        new OA\Property(
            "instructionalNotes",
            description: "Instructional notes",
            type: "string"
        ),
        new OA\Property(
            "sessionTypeId",
            description: "Session type ID",
            type: "integer"
        ),
        new OA\Property(
            "sessionTypeTitle",
            description: "Session type title",
            type: "string"
        ),
        new OA\Property(
            "sessionObjectives",
            description: "Session objectives",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "courseObjectives",
            description: "Course objectives",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "competencies",
            description: "Competencies",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "postrequisites",
            description: "Postrequisites",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "cohorts",
            description: "Cohorts",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "prerequisites",
            description: "Prerequisites",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "school",
            description: "School",
            type: "integer"
        ),
        new OA\Property(
            "courseLevel",
            description: "Course level",
            type: "integer"
        ),
        new OA\Property(
            "sessionTerms",
            description: "Session vocabulary terms",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "courseTerms",
            description: "Course vocabulary terms",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "user",
            description: "User",
            type: "string"
        ),
    ]
)]
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
    public function clearDataForUnprivilegedUsers(DateTime $dateTime): void
    {
        $this->instructionalNotes = null;
        $this->clearDataForDraftOrScheduledEvent();
        $this->removeMaterialsInDraft();
        $this->clearTimedMaterials($dateTime);
    }
    protected function clearTimedMaterials(DateTime $dateTime): void
    {
        /** @var UserMaterial $lm */
        foreach ($this->learningMaterials as $lm) {
            $lm->clearTimedMaterial($dateTime);
        }
    }
}
