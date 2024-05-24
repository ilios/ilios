<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attributes as IA;
use DateTime;
use OpenApi\Attributes as OA;

#[IA\DTO('meshDescriptors')]
#[IA\ExposeGraphQL]
#[OA\Schema(
    title: "MeshConcept",
    properties: [
        new OA\Property(
            "id",
            description: "ID",
            type: "string"
        ),
        new OA\Property(
            "name",
            description: "Name",
            type: "string"
        ),
        new OA\Property(
            "annotation",
            description: "Annotation",
            type: "string"
        ),
        new OA\Property(
            "createdAt",
            description: "Created at",
            type: "string",
            format: "date-time"
        ),
        new OA\Property(
            "updatedAt",
            description: "Updated at",
            type: "string",
            format: "date-time"
        ),
        new OA\Property(
            "previousIndexing",
            description: "MeSH previous indexing",
            type: "integer"
        ),
        new OA\Property(
            "deleted",
            description: "Is deleted",
            type: "boolean"
        ),
        new OA\Property(
            "courses",
            description: "Courses",
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
        new OA\Property(
            "sessions",
            description: "Sessions",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "concepts",
            description: "MeSH concepts",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "qualifiers",
            description: "MeSH qualifiers",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "trees",
            description: "MeSH trees",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "sessionLearningMaterials",
            description: "Session learning materials",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "courseLearningMaterials",
            description: "Course learning materials",
            type: "array",
            items: new OA\Items(type: "string")
        ),
    ]
)]
#[IA\FilterableBy('learningMaterials', IA\Type::INTEGERS)]
#[IA\FilterableBy('terms', IA\Type::INTEGERS)]
#[IA\FilterableBy('sessionTypes', IA\Type::INTEGERS)]
#[IA\FilterableBy('schools', IA\Type::INTEGERS)]
class MeshDescriptorDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('string')]
    public string $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $name;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $annotation;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public DateTime $createdAt;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public DateTime $updatedAt;

    #[IA\Expose]
    #[IA\Related('meshPreviousIndexings')]
    #[IA\Type('integer')]
    public ?int $previousIndexing = null;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $deleted;

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
    #[IA\Related('sessionObjectives')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $sessionObjectives = [];

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
    #[IA\Related('programYearObjectives')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $programYearObjectives = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $sessions = [];

    /**
     * @var string[]
     */
    #[IA\Expose]
    #[IA\Related('meshConcepts')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $concepts = [];

    /**
     * @var string[]
     */
    #[IA\Expose]
    #[IA\Related('meshQualifiers')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $qualifiers = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('meshTrees')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $trees = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $sessionLearningMaterials = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $courseLearningMaterials = [];

    public function __construct(
        string $id,
        string $name,
        ?string $annotation,
        DateTime $createdAt,
        DateTime $updatedAt,
        bool $deleted
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->annotation = $annotation;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->deleted = $deleted;
    }
}
