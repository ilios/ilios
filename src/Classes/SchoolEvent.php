<?php

declare(strict_types=1);

namespace App\Classes;

use App\Attributes as IA;
use DateTime;
use OpenApi\Attributes as OA;

#[IA\DTO('schoolEvent')]
#[OA\Schema(
    title: "SchoolEvent",
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
    ]
)]

class SchoolEvent extends CalendarEvent
{
    /**
     * Creates a new school event from a given school id and a given calendar event.
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
    public function clearDataForUnprivilegedUsers(): void
    {
        $this->instructionalNotes = null;
        $this->clearDataForDraftOrScheduledEvent();
        $this->removeMaterialsInDraft();
        array_walk($this->learningMaterials, function (UserMaterial $lm): void {
            $lm->clearMaterial();
        });
    }
    /**
     * Clear out all draft and schedule events as well as LMs based on time
     */
    public function clearDataForStudentAssociatedWithEvent(DateTime $dateTime): void
    {
        $this->instructionalNotes = null;
        $this->clearDataForDraftOrScheduledEvent();
        $this->removeMaterialsInDraft();
        array_walk($this->learningMaterials, function (UserMaterial $lm) use ($dateTime): void {
            $lm->clearTimedMaterial($dateTime);
        });
    }
}
