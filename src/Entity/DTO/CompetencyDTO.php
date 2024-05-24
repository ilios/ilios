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
            type: "integer"
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
#[IA\FilterableBy('schools', IA\Type::INTEGERS)]
#[IA\FilterableBy('courses', IA\Type::INTEGERS)]
#[IA\FilterableBy('terms', IA\Type::INTEGERS)]
#[IA\FilterableBy('sessions', IA\Type::INTEGERS)]
#[IA\FilterableBy('sessionTypes', IA\Type::INTEGERS)]
#[IA\FilterableBy('academicYears', IA\Type::INTEGERS)]
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
    #[IA\Type(IA\Type::INTEGERS)]
    public array $children = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $aamcPcrses = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $programYears = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('programYearObjectives')]
    #[IA\Type(IA\Type::INTEGERS)]
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
