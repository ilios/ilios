<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;

/**
 * Class CurriculumInventorySequenceBlockDTO
 */
#[IA\DTO('curriculumInventorySequences')]
class CurriculumInventorySequenceDTO
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
    public ?string $description;

    /**
     * Needed for voting not exposed in the API
     */
    public int $school;

    public function __construct(
        int $id,
        ?string $description
    ) {
        $this->id = $id;
        $this->description = $description;
    }
}
