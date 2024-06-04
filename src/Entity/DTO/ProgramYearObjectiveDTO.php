<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attributes as IA;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Ignore;

#[IA\DTO('programYearObjectives')]
#[IA\ExposeGraphQL]
#[OA\Schema(
    title: "ProgramYearObjective",
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
            "competency",
            description: "Competency",
            type: "integer"
        ),
        new OA\Property(
            "programYear",
            description: "Program year",
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
            "courseObjectives",
            description: "Course objectives",
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
class ProgramYearObjectiveDTO
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
    #[IA\Related('competencies')]
    #[IA\Type('integer')]
    public ?int $competency = null;

    #[IA\Expose]
    #[IA\Related('programYears')]
    #[IA\Type('integer')]
    public int $programYear;

    #[IA\Expose]
    #[IA\Related('programYearObjectives')]
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
    #[IA\Related('courseObjectives')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $courseObjectives = [];

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
    #[IA\Related('programYearObjectives')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $descendants = [];

    /**
     * Needed for Voting, not exposed in the API
     */
    #[IA\Type('boolean')]
    #[Ignore]
    public bool $programYearIsLocked;

    /**
     * Needed for Voting, not exposed in the API
     */
    #[IA\Type('boolean')]
    #[Ignore]
    public bool $programYearIsArchived;

    public function __construct(int $id, string $title, int $position, bool $active)
    {
        $this->id = $id;
        $this->title = $title;
        $this->position = $position;
        $this->active = $active;
    }
}
