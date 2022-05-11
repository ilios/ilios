<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;

#[IA\DTO('curriculumInventoryAcademicLevels')]
#[IA\ExposeGraphQL]
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
    #[IA\Type('array<string>')]
    public array $startingSequenceBlocks = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('curriculumInventorySequenceBlocks')]
    #[IA\Type('array<string>')]
    public array $endingSequenceBlocks = [];

    /**
     * Needed for voting not exposed in the API
     */
    public int $school;

    public function __construct(int $id, string $name, ?string $description, int $level)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->level = $level;
    }
}
