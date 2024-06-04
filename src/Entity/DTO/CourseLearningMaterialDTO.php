<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attributes as IA;
use DateTime;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Ignore;

#[IA\DTO('courseLearningMaterials')]
#[IA\ExposeGraphQL]
#[OA\Schema(
    title: "CourseLearningMaterial",
    properties: [
        new OA\Property(
            "id",
            description: "ID",
            type: "integer"
        ),
        new OA\Property(
            "notes",
            description: "Notes",
            type: "string"
        ),
        new OA\Property(
            "required",
            description: "Required",
            type: "boolean"
        ),
        new OA\Property(
            "publicNotes",
            description: "Has public notes",
            type: "boolean"
        ),
        new OA\Property(
            "position",
            description: "Position",
            type: "integer"
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
            "course",
            description: "Course",
            type: "integer"
        ),
        new OA\Property(
            "learningMaterial",
            description: "Learning material",
            type: "integer"
        ),
        new OA\Property(
            "meshDescriptors",
            description: "MeSH descriptors",
            type: "array",
            items: new OA\Items(type: "string")
        ),
    ]
)]
class CourseLearningMaterialDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $notes;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $required;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $publicNotes;

    #[IA\Expose]
    #[IA\Type('integer')]
    public int $position;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public ?DateTime $startDate;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public ?DateTime $endDate;

    #[IA\Expose]
    #[IA\Related('courses')]
    #[IA\Type('integer')]
    public int $course;

    #[IA\Expose]
    #[IA\Related('learningMaterials')]
    #[IA\Type('integer')]
    public int $learningMaterial;

    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::STRINGS)]
    public array $meshDescriptors = [];

    /**
     * Needed for Voting, not exposed in the API
     */
    #[Ignore]
    public int $school;

    /**
     * Needed for Voting, not exposed in the API
     */
    #[Ignore]
    public int $status;

    /**
     * Needed for Voting, not exposed in the API
     */
    #[Ignore]
    public bool $courseIsLocked;

    /**
     * Needed for Voting, not exposed in the API
     */
    #[Ignore]
    public bool $courseIsArchived;

    public function __construct(
        int $id,
        ?string $notes,
        bool $required,
        bool $publicNotes,
        int $position,
        ?DateTime $startDate,
        ?DateTime $endDate
    ) {
        $this->id = $id;
        $this->notes = $notes;
        $this->required = $required;
        $this->publicNotes = $publicNotes;
        $this->position = $position;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }
}
