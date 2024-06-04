<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attributes as IA;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Ignore;

#[IA\DTO('courseObjectives')]
#[IA\ExposeGraphQL]
#[OA\Schema(
    title: "CourseObjective",
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
            "active",
            description: "Is active",
            type: "boolean"
        ),
        new OA\Property(
            "position",
            description: "Position",
            type: "integer"
        ),
        new OA\Property(
            "course",
            description: "Course",
            type: "integer"
        ),
        new OA\Property(
            "ancestor",
            description: "Ancestor",
            type: "integer"
        ),
        new OA\Property(
            "terms",
            description: "Vocabulary terms",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "programYearObjectives",
            description: "Program-year objectives",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "sessionObjectives",
            description: "Session objectives",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "meshDescriptors",
            description: "MeSH descriptors",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "descendants",
            description: "Descendants",
            type: "array",
            items: new OA\Items(type: "string")
        ),
    ]
)]
#[IA\FilterableBy('courses', IA\Type::INTEGERS)]
class CourseObjectiveDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $title;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $active;

    #[IA\Expose]
    #[IA\Type('integer')]
    public int $position;

    #[IA\Expose]
    #[IA\Related('courses')]
    #[IA\Type('integer')]
    public int $course;

    #[IA\Expose]
    #[IA\Related('courseObjectives')]
    #[IA\Type('integer')]
    public ?int $ancestor = null;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $terms = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('programYearObjectives')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $programYearObjectives = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('sessionObjectives')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $sessionObjectives = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::STRINGS)]
    public array $meshDescriptors = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('courseObjectives')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $descendants = [];

    /**
     * Needed for Voting, not exposed in the API
     */
    #[Ignore]
    public int $school;

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

    public function __construct(int $id, string $title, int $position, bool $active)
    {
        $this->id = $id;
        $this->title = $title;
        $this->position = $position;
        $this->active = $active;
    }
}
