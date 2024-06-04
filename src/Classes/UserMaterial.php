<?php

declare(strict_types=1);

namespace App\Classes;

use App\Attributes as IA;
use DateTime;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Ignore;

#[IA\DTO('userMaterial')]
#[OA\Schema(
    title: "UserMaterial",
    properties: [
        new OA\Property(
            "id",
            description: "ID of the learning material",
            type: "string"
        ),
        new OA\Property(
            "session",
            description: "Session ID",
            type: "string"
        ),
        new OA\Property(
            "course",
            description:"Course ID",
            type:"string",
        ),
        new OA\Property(
            "publicNotes",
            description: "Public notes",
            type: "string"
        ),
        new OA\Property(
            "required",
            description: "Is required",
            type: "boolean"
        ),
        new OA\Property(
            "title",
            description: "Title",
            type: "string"
        ),
        new OA\Property(
            "description",
            description: "Description",
            type: "string"
        ),
        new OA\Property(
            "originalAuthor",
            description: "The creator of this material",
            type: "string"
        ),
        new OA\Property(
            "absoluteFileUri",
            description: "File URL",
            type: "string"
        ),
        new OA\Property(
            "citation",
            description: "Citation",
            type: "string"
        ),
        new OA\Property(
            "link",
            description: "Link",
            type: "string"
        ),
        new OA\Property(
            "filename",
            description: "Filesize in bytes",
            type: "integer"
        ),
        new OA\Property(
            "mimetype",
            description: "Mime type",
            type: "string"
        ),
        new OA\Property(
            "sessionTitle",
            description: "Session title",
            type: "string"
        ),
        new OA\Property(
            "courseTitle",
            description: "Course title",
            type: "string"
        ),
        new OA\Property(
            "courseExternalId",
            description: "Course External ID",
            type: "string"
        ),
        new OA\Property(
            "courseYear",
            description: "Course Year",
            type: "integer"
        ),
        new OA\Property(
            "firstOfferingDate",
            description: "The first appearance of this material",
            type: "string",
            format: "date-time"
        ),
        new OA\Property(
            "courseLearningMaterial",
            description: "ID of the course/learning-material association",
            type: "string",
        ),
        new OA\Property(
            "sessionLearningMaterial",
            description: "ID of the session/learning-material association",
            type: "string",
        ),
        new OA\Property(
            "filesize",
            description: "Filesize in bytes",
            type: "string",
        ),
        new OA\Property(
            "position",
            description: "Sorting position of this material in the context of a course or session association",
            type: "string",
        ),
        new OA\Property(
            "instructors",
            description: "Instructor names",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "startDate",
            description: "Timed-release start date-time",
            type: "string",
            format: "date-time"
        ),
        new OA\Property(
            "endDate",
            description: "Timed-release end date-time",
            type: "string",
            format: "date-time"
        ),
        new OA\Property(
            "isBlanked",
            description: "Is blanked",
            type: "boolean"
        ),
    ]
)]
class UserMaterial
{
    /**
     * A list of 'do not scrub' properties.
     */
    protected static array $doNotScrubProps = [
        'id',
        'courseLearningMaterial',
        'sessionLearningMaterial',
        'position',
        'title',
        'course',
        'courseTitle',
        'courseExternalId',
        'courseYear',
        'session',
        'sessionTitle',
        'startDate',
        'endDate',
        'isBlanked',
        'firstOfferingDate',
    ];

    #[IA\Expose]
    #[IA\Type('string')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?int $courseLearningMaterial = null;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?int $sessionLearningMaterial = null;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?int $session = null;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?int $course = null;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $publicNotes = null;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public ?bool $required = null;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $title;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $description = null;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $originalAuthor = null;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $absoluteFileUri = null;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $citation = null;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $link = null;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $filename = null;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?int $filesize = null;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?int $position = null;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $mimetype = null;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $sessionTitle = null;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $courseTitle = null;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $courseExternalId = null;

    #[IA\Expose]
    #[IA\Type('int')]
    public int $courseYear;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public ?DateTime $firstOfferingDate = null;

    #[IA\Expose]
    #[IA\Type(IA\Type::STRINGS)]
    public array $instructors = [];

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public ?DateTime $startDate = null;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public ?DateTime $endDate = null;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $isBlanked = false;

    #[Ignore]
    public ?int $status = null;
    /**
     * Blanks out properties of timed learning materials that are outside their given
     * timed-release window (relative to the given date-time).
     * And sets the isBlanked flag to TRUE, as applicable.
     */
    public function clearTimedMaterial(DateTime $dateTime)
    {
        $startDate = $this->startDate;
        $endDate = $this->endDate;
        $blankThis = false;
        if (isset($startDate) && isset($endDate)) {
            $blankThis = ($startDate > $dateTime || $dateTime > $endDate);
        } elseif (isset($startDate)) {
            $blankThis = ($startDate > $dateTime);
        } elseif (isset($endDate)) {
            $blankThis = ($dateTime > $endDate);
        }

        if ($blankThis) {
            $this->clearMaterial();
        }
    }
    /**
     * Blanks out most data points of this learning material.
     */
    public function clearMaterial()
    {
        $this->isBlanked = true;
        $props = array_keys(get_object_vars($this));
        foreach ($props as $prop) {
            if (! in_array($prop, self::$doNotScrubProps) && $prop !== 'instructors') {
                $this->$prop = null;
            }
        }
        $this->instructors = [];
    }
}
