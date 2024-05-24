<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attributes as IA;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Ignore;

#[IA\DTO('curriculumInventoryAcademicLevels')]
#[IA\ExposeGraphQL]
#[OA\Schema(
    title: "CurriculumInventoryAcademicLevel",
    properties: [
        new OA\Property(
            "id",
            description: "ID",
            type: "integer"
        ),
        new OA\Property(
            "name",
            description: "Name",
            type: "string"
        ),
        new OA\Property(
            "description",
            description: "Description",
            type: "string"
        ),
        new OA\Property(
            "level",
            description: "Level",
            type: "integer"
        ),
        new OA\Property(
            "report",
            description: "Curriculum inventory report",
            type: "integer"
        ),
        new OA\Property(
            "startingSequenceBlocks",
            description: "Starting sequence blocks",
            type: "array",
            items: new OA\Items(type: "string")
        ),
        new OA\Property(
            "endingSequenceBlocks",
            description: "Ending sequence blocks",
            type: "array",
            items: new OA\Items(type: "string")
        ),
    ]
)]
class CurriculumInventoryAcademicLevelDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $name;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $description;

    #[IA\Expose]
    #[IA\Type('integer')]
    public int $level;

    #[IA\Expose]
    #[IA\Related('curriculumInventoryReports')]
    #[IA\Type('integer')]
    public int $report;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('curriculumInventorySequenceBlocks')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $startingSequenceBlocks = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('curriculumInventorySequenceBlocks')]
    #[IA\Type(IA\Type::INTEGERS)]
    public array $endingSequenceBlocks = [];

    /**
     * Needed for voting not exposed in the API
     */
    #[Ignore]
    public int $school;

    public function __construct(int $id, string $name, ?string $description, int $level)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->level = $level;
    }
}
