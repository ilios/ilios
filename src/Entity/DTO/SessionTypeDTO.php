<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attributes as IA;
use OpenApi\Attributes as OA;

#[IA\DTO('sessionTypes')]
#[IA\ExposeGraphQL]
#[OA\Schema(
    title: "SessionType",
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
            "calendarColor",
            description: "Calendar color",
            type: "string"
        ),
        new OA\Property(
            "active",
            description: "Is active",
            type: "boolean"
        ),
        new OA\Property(
            "assessment",
            description: "Is an assessment",
            type: "boolean"
        ),
        new OA\Property(
            "assessmentOption",
            description: "Assessment option",
            type: "integer"
        ),
        new OA\Property(
            "school",
            description: "School",
            type: "integer"
        ),
        new OA\Property(
            "aamcMethods",
            description: "AAMC methods",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "sessions",
            description: "Sessions",
            type: "array",
            items: new OA\Items(type: "string")
        )
    ]
)]
#[IA\FilterableBy('courses', 'array<integer>')]
#[IA\FilterableBy('learningMaterials', 'array<integer>')]
#[IA\FilterableBy('instructors', 'array<integer>')]
#[IA\FilterableBy('instructorGroups', 'array<integer>')]
#[IA\FilterableBy('programs', 'array<integer>')]
#[IA\FilterableBy('competencies', 'array<integer>')]
#[IA\FilterableBy('terms', 'array<integer>')]
#[IA\FilterableBy('schools', 'array<integer>')]
#[IA\FilterableBy('meshDescriptors', 'array<string>')]
class SessionTypeDTO
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
    public string $calendarColor;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $active;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $assessment;

    #[IA\Expose]
    #[IA\Related('assessmentOptions')]
    #[IA\Type('integer')]
    public ?int $assessmentOption = null;

    #[IA\Expose]
    #[IA\Related('schools')]
    #[IA\Type('integer')]
    public int $school;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<integer>')]
    public array $aamcMethods = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<integer>')]
    public array $sessions = [];

    public function __construct(int $id, string $title, string $calendarColor, bool $assessment, bool $active)
    {
        $this->id = $id;
        $this->title = $title;
        $this->calendarColor = $calendarColor;
        $this->assessment = $assessment;
        $this->active = $active;
    }
}
