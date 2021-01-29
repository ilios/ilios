<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;
use App\Entity\CurriculumInventoryAcademicLevelInterface;
use App\Entity\CurriculumInventoryReportInterface;
use App\Entity\CurriculumInventorySequenceBlockInterface;
use DateTime;

/**
 * Class CurriculumInventoryReport
 *
 * @IS\DTO("curriculumInventoryReports")
 */
class CurriculumInventoryReportDTO
{
    /**
     * @IS\Id
     * @IS\Expose
     * @IS\Type("integer")
     */
    public int $id;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $name;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $description;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public int $year;

    /**
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public DateTime $startDate;

    /**
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public DateTime $endDate;

    /**
     * @IS\Expose
     * @IS\Related("curriculumInventoryExports")
     * @IS\Type("integer")
     */
    public ?int $export = null;

    /**
     * @IS\Expose
     * @IS\Related("curriculumInventorySequences")
     * @IS\Type("integer")
     */
    public ?int $sequence = null;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("curriculumInventorySequenceBlocks")
     * @IS\Type("array<string>")
     */
    public array $sequenceBlocks = [];

    /**
     * @IS\Expose
     * @IS\Related("programs")
     * @IS\Type("integer")
     */
    public int $program;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("curriculumInventoryAcademicLevels")
     * @IS\Type("array<string>")
     */
    public array $academicLevels = [];

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
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
     * @IS\Expose
     * @IS\Related("users")
     * @IS\Type("array<string>")
     */
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
