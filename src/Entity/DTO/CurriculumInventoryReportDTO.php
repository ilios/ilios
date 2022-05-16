<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;
use App\Entity\CurriculumInventoryAcademicLevelInterface;
use App\Entity\CurriculumInventoryReportInterface;
use App\Entity\CurriculumInventorySequenceBlockInterface;
use DateTime;

#[IA\DTO('curriculumInventoryReports')]
#[IA\ExposeGraphQL]
class CurriculumInventoryReportDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $name;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $description;

    #[IA\Expose]
    #[IA\Type('string')]
    public int $year;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public DateTime $startDate;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public DateTime $endDate;

    #[IA\Expose]
    #[IA\Related('curriculumInventoryExports')]
    #[IA\Type('integer')]
    public ?int $export = null;

    #[IA\Expose]
    #[IA\Related('curriculumInventorySequences')]
    #[IA\Type('integer')]
    public ?int $sequence = null;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('curriculumInventorySequenceBlocks')]
    #[IA\Type('array<string>')]
    public array $sequenceBlocks = [];

    #[IA\Expose]
    #[IA\Related('programs')]
    #[IA\Type('integer')]
    public int $program;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('curriculumInventoryAcademicLevels')]
    #[IA\Type('array<string>')]
    public array $academicLevels = [];

    #[IA\Expose]
    #[IA\Type('string')]
    public string $absoluteFileUri;

    /**
     * Needed for voting not exposed in the API
     */
    public int $school;

    /**
     * Needed for creating the absolute URL, not exposed in the API
     */
    public ?string $token;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('users')]
    #[IA\Type('array<string>')]
    public array $administrators = [];

    public function __construct(
        int $id,
        ?string $name,
        ?string $description,
        int $year,
        DateTime $startDate,
        DateTime $endDate,
        ?string $token
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->year = $year;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->token = $token;
    }
}
