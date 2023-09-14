<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attributes as IA;
use OpenApi\Attributes as OA;

#[IA\DTO('programs')]
#[IA\ExposeGraphQL]
#[OA\Schema(
    title: "Program",
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
            "shortTitle",
            description: "Short title",
            type: "string"
        ),
        new OA\Property(
            "duration",
            description: "Duration",
            type: "string"
        ),
        new OA\Property(
            "school",
            description: "School",
            type: "integer"
        ),
        new OA\Property(
            "programYears",
            description: "Program years",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "curriculumInventoryReports",
            description: "Curriculum inventory reports",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "directors",
            description: "Directors",
            type: "array",
            items: new OA\Items(type: "string")
        ),
    ]
)]
#[IA\FilterableBy('courses', 'array<integer>')]
#[IA\FilterableBy('sessions', 'array<integer>')]
#[IA\FilterableBy('terms', 'array<integer>')]
#[IA\FilterableBy('schools', 'array<integer>')]
class ProgramDTO
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
    public ?string $shortTitle;

    #[IA\Expose]
    #[IA\Type('integer')]
    public int $duration;

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
    public array $programYears = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<integer>')]
    public array $curriculumInventoryReports = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('users')]
    #[IA\Type('array<integer>')]
    public array $directors = [];

    public function __construct(
        int $id,
        string $title,
        ?string $shortTitle,
        int $duration
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->shortTitle = $shortTitle;
        $this->duration = $duration;
    }
}
