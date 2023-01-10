<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attributes as IA;
use DateTime;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Ignore;

#[IA\DTO('curriculumInventoryExports')]
#[IA\ExposeGraphQL]
#[OA\Schema(
    title: "CurriculumInventoryExport",
    properties: [
        new OA\Property(
            "id",
            description: "ID",
            type: "integer"
        ),
        new OA\Property(
            "report",
            description: "Curriculum inventory report",
            type: "integer"
        ),
        new OA\Property(
            "createdBy",
            description: "Created by user",
            type: "string"
        ),
        new OA\Property(
            "createdAt",
            description: "Created at",
            type: "string",
            format: "date-time"
        )
    ]
)]
class CurriculumInventoryExportDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Related('curriculumInventoryReports')]
    #[IA\Type('integer')]
    public int $report;

    #[IA\Expose]
    #[IA\Type('string')]
    public int $createdBy;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public DateTime $createdAt;

    /**
     * Not exposed.
     */
    #[IA\Type('string')]
    #[Ignore]
    public string $document;

    public function __construct(int $id, string $document, DateTime $createdAt)
    {
        $this->id = $id;
        $this->document = $document;
        $this->createdAt = $createdAt;
    }
}
