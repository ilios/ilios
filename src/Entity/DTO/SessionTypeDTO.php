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
        ),
    ]
)]
#[IA\FilterableBy('courses', IA\Type::INTEGERS)]
#[IA\FilterableBy('learningMaterials', IA\Type::INTEGERS)]
#[IA\FilterableBy('instructors', IA\Type::INTEGERS)]
#[IA\FilterableBy('instructorGroups', IA\Type::INTEGERS)]
#[IA\FilterableBy('programs', IA\Type::INTEGERS)]
#[IA\FilterableBy('competencies', IA\Type::INTEGERS)]
#[IA\FilterableBy('terms', IA\Type::INTEGERS)]
#[IA\FilterableBy('schools', IA\Type::INTEGERS)]
#[IA\FilterableBy('meshDescriptors', IA\Type::STRINGS)]
#[IA\FilterableBy('academicYears', IA\Type::INTEGERS)]
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
    #[IA\Type(IA\Type::INTEGERS)]
    public array $aamcMethods = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
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
