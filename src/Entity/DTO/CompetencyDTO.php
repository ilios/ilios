<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attributes as IA;
use OpenApi\Attributes as OA;

#[IA\DTO('competencies')]
#[IA\ExposeGraphQL]
#[OA\Schema(
    title: "Competency",
    properties: [
        new OA\Property(
            "id",
            description: "ID",
            type: "integer",
            readOnly: true,
        ),
        new OA\Property(
            "title",
            description: "Title",
            type: "string"
        ),
        new OA\Property(
            "school",
            description:"School",
            type:"integer"
        ),
        new OA\Property(
            "active",
            description:"Active",
            type:"boolean"
        ),
        new OA\Property(
            "parent",
            description:"Parent competency",
            type:"integer"
        ),
        new OA\Property(
            "children",
            description: "Sub-competencies",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "aamcPcrses",
            description: "AAMC PCRSes",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "programYears",
            description: "Program years",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "programYearObjectives",
            description: "Program year objectives",
            type: "array",
            items: new OA\Items(type: "string")
        ),
    ]
)]
#[IA\FilterableBy('schools', 'array<integer>')]
#[IA\FilterableBy('courses', 'array<integer>')]
#[IA\FilterableBy('terms', 'array<integer>')]
#[IA\FilterableBy('sessions', 'array<integer>')]
#[IA\FilterableBy('sessionTypes', 'array<integer>')]
class CompetencyDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $title;

    #[IA\Expose]
    #[IA\Related('schools')]
    #[IA\Type('integer')]
    public int $school;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $active;

    #[IA\Expose]
    #[IA\Related('competencies')]
    #[IA\Type('integer')]
    public ?int $parent = null;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('competencies')]
    #[IA\Type('array<integer>')]
    public array $children = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<integer>')]
    public array $aamcPcrses = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<integer>')]
    public array $programYears = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('programYearObjectives')]
    #[IA\Type('array<integer>')]
    public array $programYearObjectives = [];

    public function __construct(
        int $id,
        ?string $title,
        bool $active
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->active = $active;
    }
}
