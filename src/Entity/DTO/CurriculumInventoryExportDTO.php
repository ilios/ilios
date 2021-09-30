<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;
use DateTime;

/**
 * Class CurriculumInventoryExport
 */
#[IA\DTO('curriculumInventoryExports')]
#[IA\ExposeGraphQL]
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

    /**
     * Not exposed.
     */
    #[IA\Type('string')]
    public string $document;

    #[IA\Expose]
    #[IA\Type('string')]
    public int $createdBy;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public DateTime $createdAt;

    public function __construct(int $id, string $document, DateTime $createdAt)
    {
        $this->id = $id;
        $this->document = $document;
        $this->createdAt = $createdAt;
    }
}
