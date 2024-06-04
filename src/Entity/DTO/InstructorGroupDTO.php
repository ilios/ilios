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
            type: "integer"
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
#[IA\FilterableBy('schools', IA\Type::INTEGERS)]
#[IA\FilterableBy('courses', IA\Type::INTEGERS)]
#[IA\FilterableBy('sessions', IA\Type::INTEGERS)]
#[IA\FilterableBy('sessionTypes', IA\Type::INTEGERS)]
#[IA\FilterableBy('learningMaterials', IA\Type::INTEGERS)]
#[IA\FilterableBy('instructors', IA\Type::INTEGERS)]
#[IA\FilterableBy('terms', IA\Type::INTEGERS)]
#[IA\FilterableBy('academicYears', IA\Type::INTEGERS)]
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
    #[IA\Type(IA\Type::INTEGERS)]
    public array $learnerGroups = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $ilmSessions = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $users = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $offerings = [];

    public function __construct(int $id, string $title)
    {
        $this->id = $id;
        $this->title = $title;
    }
}
