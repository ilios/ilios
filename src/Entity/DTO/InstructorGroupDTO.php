<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attributes as IA;
use OpenApi\Attributes as OA;

#[IA\DTO('instructorGroups')]
#[IA\ExposeGraphQL]
#[OA\Schema(
    title: "InstructorGroup",
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
            description: "School",
            type: "integer"
        ),
        new OA\Property(
            "learnerGroups",
            description: "Learner groups",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "ilmSessions",
            description: "ILM sessions",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "users",
            description: "Instructors",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "offerings",
            description: "Offerings",
            type: "array",
            items: new OA\Items(type: "string")
        ),
    ]
)]
#[IA\FilterableBy('schools', 'array<integer>')]
#[IA\FilterableBy('courses', 'array<integer>')]
#[IA\FilterableBy('sessions', 'array<integer>')]
#[IA\FilterableBy('sessionTypes', 'array<integer>')]
#[IA\FilterableBy('learningMaterials', 'array<integer>')]
#[IA\FilterableBy('instructors', 'array<integer>')]
#[IA\FilterableBy('terms', 'array<integer>')]
class InstructorGroupDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $title;

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
    public array $learnerGroups = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<integer>')]
    public array $ilmSessions = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<integer>')]
    public array $users = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<integer>')]
    public array $offerings = [];

    public function __construct(int $id, string $title)
    {
        $this->id = $id;
        $this->title = $title;
    }
}
