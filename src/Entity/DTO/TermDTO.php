<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attributes as IA;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Ignore;

#[IA\DTO('terms')]
#[IA\ExposeGraphQL]
#[OA\Schema(
    title: "Term",
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
            "description",
            description: "Description",
            type: "string"
        ),
        new OA\Property(
            "active",
            description: "Is active",
            type: "boolean"
        ),
        new OA\Property(
            "vocabulary",
            description: "Vocabulary",
            type: "integer"
        ),
        new OA\Property(
            "parent",
            description: "Parent term",
            type: "integer"
        ),
        new OA\Property(
            "children",
            description: "Child terms",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "courses",
            description: "Courses",
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
            "sessions",
            description: "Sessions",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "aamcResourceTypes",
            description: "AAMC resource types",
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
            "courseObjectives",
            description: "Course objectives",
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
#[IA\FilterableBy('sessionTypes', IA\Type::INTEGERS)]
#[IA\FilterableBy('instructors', IA\Type::INTEGERS)]
#[IA\FilterableBy('instructorGroups', IA\Type::INTEGERS)]
#[IA\FilterableBy('learningMaterials', IA\Type::INTEGERS)]
#[IA\FilterableBy('competencies', IA\Type::INTEGERS)]
#[IA\FilterableBy('meshDescriptors', IA\Type::STRINGS)]
#[IA\FilterableBy('programs', IA\Type::INTEGERS)]
#[IA\FilterableBy('schools', IA\Type::INTEGERS)]
#[IA\FilterableBy('academicYears', IA\Type::INTEGERS)]
class TermDTO
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
    public ?string $description;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $active;

    #[IA\Expose]
    #[IA\Related('vocabularies')]
    #[IA\Type('integer')]
    public int $vocabulary;

    #[IA\Expose]
    #[IA\Related('terms')]
    #[IA\Type('integer')]
    public ?int $parent = null;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('terms')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $children = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $courses = [];

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
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $sessions = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::STRINGS)]
    public array $aamcResourceTypes = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $sessionObjectives = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $courseObjectives = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $programYearObjectives = [];

    /**
     * For Voter use, not public
     */
    #[Ignore]
    public int $school;

    public function __construct(
        int $id,
        string $title,
        ?string $description,
        bool $active
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->active = $active;
    }
}
