<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attributes as IA;
use OpenApi\Attributes as OA;

#[IA\DTO('learnerGroups')]
#[IA\ExposeGraphQL]
#[OA\Schema(
    title: "LearnerGroup",
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
            "needsAccommodation",
            description: "One or more learners in this group need special accommodation",
            type: "boolean"
        ),
        new OA\Property(
            "cohort",
            description: "Cohort",
            type: "integer",
        ),
        new OA\Property(
            "parent",
            description: "Parent learner group",
            type: "integer"
        ),
        new OA\Property(
            "ancestor",
            description: "Ancestor",
            type: "integer"
        ),
        new OA\Property(
            "descendants",
            description: "Descendants",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "children",
            description: "Child learner groups",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "ilmSessions",
            description: "ILM Sessions",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "offerings",
            description: "Offerings",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "instructorGroups",
            description: "Instructor groups",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "users",
            description: "Learners",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "instructors",
            description: "Instructors",
            type: "array",
            items: new OA\Items(type: "string")
        ),
    ]
)]
#[IA\FilterableBy('cohorts', IA\Type::INTEGERS)]
class LearnerGroupDTO
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
    public ?string $location;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $url;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $needsAccommodation;

    #[IA\Expose]
    #[IA\Related('cohorts')]
    #[IA\Type('integer')]
    public int $cohort;

    #[IA\Expose]
    #[IA\Related('learnerGroups')]
    #[IA\Type('integer')]
    public ?int $parent = null;

    #[IA\Expose]
    #[IA\Related('learnerGroups')]
    #[IA\Type('integer')]
    public ?int $ancestor = null;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('learnerGroups')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $descendants = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('learnerGroups')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $children = [];

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
    public array $offerings = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $instructorGroups = [];

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
    #[IA\Related('users')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $instructors = [];

    public function __construct(int $id, string $title, ?string $location, ?string $url, bool $needsAccommodation)
    {
        $this->id = $id;
        $this->title = $title;
        $this->location = $location;
        $this->url = $url;
        $this->needsAccommodation = $needsAccommodation;
    }
}
