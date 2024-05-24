<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attributes as IA;
use DateTime;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Ignore;

#[IA\DTO('curriculumInventorySequenceBlocks')]
#[IA\ExposeGraphQL]
#[OA\Schema(
    title: "CurriculumInventorySequenceBlock",
    properties: [
        new OA\Property(
            "id",
            description: "ID",
            type: "integer"
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
            "required",
            description: "Required",
            type: "integer"
        ),
        new OA\Property(
            "childSequenceOrder",
            description: "Child-sequence order",
            type: "integer"
        ),
        new OA\Property(
            "orderInSequence",
            description: "Order in sequence",
            type: "integer"
        ),
        new OA\Property(
            "minimum",
            description: "Minimum",
            type: "integer"
        ),
        new OA\Property(
            "maximum",
            description: "Maximum",
            type: "integer"
        ),
        new OA\Property(
            "track",
            description: "Is track",
            type: "integer"
        ),
        new OA\Property(
            "startDate",
            description: "Start date",
            type: "string",
            format: 'date-time'
        ),
        new OA\Property(
            "endDate",
            description: "End date",
            type: "string",
            format: 'date-time'
        ),
        new OA\Property(
            "duration",
            description: "Duration",
            type: "integer"
        ),
        new OA\Property(
            "startingAcademicLevel",
            description: "Starting academic level",
            type: "integer"
        ),
        new OA\Property(
            "endingAcademicLevel",
            description: "Ending academic level",
            type: "integer"
        ),
        new OA\Property(
            "course",
            description: "Course",
            type: "integer"
        ),
        new OA\Property(
            "parent",
            description: "Parent sequence block",
            type: "integer"
        ),
        new OA\Property(
            "report",
            description: "Curriculum inventory report",
            type: "integer"
        ),
        new OA\Property(
            "children",
            description: "Child sequence blocks",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "sessions",
            description: "Sessions",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "excludedSessions",
            description: "Excluded sessions",
            type: "array",
            items: new OA\Items(type: "string")
        ),
    ]
)]
class CurriculumInventorySequenceBlockDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $title;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $description;

    #[IA\Expose]
    #[IA\Type('integer')]
    public int $required;

    #[IA\Expose]
    #[IA\Type('integer')]
    public int $childSequenceOrder;

    #[IA\Expose]
    #[IA\Type('integer')]
    public int $orderInSequence;

    #[IA\Expose]
    #[IA\Type('integer')]
    public int $minimum;

    #[IA\Expose]
    #[IA\Type('integer')]
    public int $maximum;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $track;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public ?DateTime $startDate;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public ?DateTime $endDate;

    #[IA\Expose]
    #[IA\Type('integer')]
    public int $duration;

    #[IA\Expose]
    #[IA\Related('curriculumInventoryAcademicLevels')]
    #[IA\Type('integer')]
    public int $startingAcademicLevel;

    #[IA\Expose]
    #[IA\Related('curriculumInventoryAcademicLevels')]
    #[IA\Type('integer')]
    public int $endingAcademicLevel;

    #[IA\Expose]
    #[IA\Related('courses')]
    #[IA\Type('integer')]
    public ?int $course = null;

    #[IA\Expose]
    #[IA\Related('curriculumInventorySequenceBlocks')]
    #[IA\Type('integer')]
    public ?int $parent = null;

    #[IA\Expose]
    #[IA\Related('curriculumInventoryReports')]
    #[IA\Type('integer')]
    public int $report;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('curriculumInventorySequenceBlocks')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $children = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $sessions = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('sessions')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $excludedSessions = [];

    /**
     * Needed for voting not exposed in the API
     */
    #[Ignore]
    public int $school;

    public function __construct(
        int $id,
        string $title,
        ?string $description,
        int $required,
        int $childSequenceOrder,
        int $orderInSequence,
        int $minimum,
        int $maximum,
        bool $track,
        ?DateTime $startDate,
        ?DateTime $endDate,
        int $duration
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->required = $required;
        $this->childSequenceOrder = $childSequenceOrder;
        $this->orderInSequence = $orderInSequence;
        $this->minimum = $minimum;
        $this->maximum = $maximum;
        $this->track = $track;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->duration = $duration;
    }
}
