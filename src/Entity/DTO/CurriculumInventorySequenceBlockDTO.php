<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;
use DateTime;

/**
 * Class CurriculumInventorySequenceBlockDTO
 */
#[IA\DTO('curriculumInventorySequenceBlocks')]
#[IA\ExposeGraphQL]
class CurriculumInventorySequenceBlockDTO
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
    #[IA\Type('integer')]
    public int $required;

    #[IA\Expose]
    #[IA\Type('integer')]
    public int $childSequenceOrder;

    #[IA\Expose]
    #[IA\Type('integer')]
    public int $orderInSequence;

    #[IA\Expose]
    #[IA\Type('integer')]
    public int $minimum;

    #[IA\Expose]
    #[IA\Type('integer')]
    public int $maximum;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $track;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public ?DateTime $startDate;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public ?DateTime $endDate;

    #[IA\Expose]
    #[IA\Type('integer')]
    public int $duration;

    #[IA\Expose]
    #[IA\Related('curriculumInventoryAcademicLevels')]
    #[IA\Type('integer')]
    public int $academicLevel;

    #[IA\Expose]
    #[IA\Related('courses')]
    #[IA\Type('integer')]
    public ?int $course = null;

    #[IA\Expose]
    #[IA\Related('curriculumInventorySequenceBlocks')]
    #[IA\Type('integer')]
    public ?int $parent = null;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('curriculumInventorySequenceBlocks')]
    #[IA\Type('array<string>')]
    public array $children = [];

    #[IA\Expose]
    #[IA\Related('curriculumInventoryReports')]
    #[IA\Type('integer')]
    public int $report;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $sessions = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('sessions')]
    #[IA\Type('array<string>')]
    public array $excludedSessions = [];

    /**
     * Needed for voting not exposed in the API
     */
    public int $school;

    public function __construct(
        int $id,
        string $title,
        ?string $description,
        int $required,
        int $childSequenceOrder,
        int $orderInSequence,
        int $minimum,
        int $maximum,
        bool $track,
        ?DateTime $startDate,
        ?DateTime $endDate,
        int $duration
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->required = $required;
        $this->childSequenceOrder = $childSequenceOrder;
        $this->orderInSequence = $orderInSequence;
        $this->minimum = $minimum;
        $this->maximum = $maximum;
        $this->track = $track;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->duration = $duration;
    }
}
