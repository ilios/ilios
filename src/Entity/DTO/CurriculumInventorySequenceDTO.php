<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Ignore;

#[IA\DTO('curriculumInventorySequences')]
#[IA\ExposeGraphQL]
#[OA\Schema(
    title: "CurriculumInventorySequence",
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
            "report",
            description: "Curriculum inventory report",
            type: "integer"
        ),
    ]
)]
class CurriculumInventorySequenceDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $description;

    #[IA\Expose]
    #[IA\Related('curriculumInventoryReports')]
    #[IA\Type('integer')]
    public int $report;

    /**
     * Needed for voting not exposed in the API
     */
    #[Ignore]
    public int $school;

    public function __construct(
        int $id,
        ?string $description
    ) {
        $this->id = $id;
        $this->description = $description;
    }
}
