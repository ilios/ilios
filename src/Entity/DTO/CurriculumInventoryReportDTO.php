<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;
use App\Entity\CurriculumInventoryAcademicLevelInterface;
use App\Entity\CurriculumInventoryReportInterface;
use App\Entity\CurriculumInventorySequenceBlockInterface;

/**
 * Class CurriculumInventoryReport
 *
 * @IS\DTO("curriculumInventoryReports")
 */
class CurriculumInventoryReportDTO
{
    /**
     * @var int
     * @IS\Id
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $name;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $description;

    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $year;

    /**
     * @var \DateTime
     *
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public $startDate;

    /**
     * @var \DateTime
     *
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public $endDate;

    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Related("curriculumInventoryExports")
     * @IS\Type("integer")
     */
    public $export;

    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Related("curriculumInventorySequences")
     * @IS\Type("integer")
     */
    public $sequence;

    /**
     * @var array
     *
     * @IS\Expose
     * @IS\Related("curriculumInventorySequenceBlocks")
     * @IS\Type("array<string>")
     */
    public $sequenceBlocks;

    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Related("programs")
     * @IS\Type("integer")
     */
    public $program;

    /**
     * @var array
     *
     * @IS\Expose
     * @IS\Related("curriculumInventoryAcademicLevels")
     * @IS\Type("array<string>")
     */
    public $academicLevels;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $absoluteFileUri;

    /**
     * Needed for voting not exposed in the API
     *
     * @var int
     */
    public $school;

    /**
     * Needed for creating the absolute URL, not exposed in the API
     *
     * @var int
     */
    public $token;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("users")
     * @IS\Type("array<string>")
     */
    public $administrators;

    /**
     * Constructor
     */
    public function __construct($id, $name, $description, $year, $startDate, $endDate, $token)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->year = $year;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->token = $token;

        $this->academicLevels = [];
        $this->sequenceBlocks = [];
        $this->administrators = [];
    }

    public static function createFromEntity(CurriculumInventoryReportInterface $report): CurriculumInventoryReportDTO
    {
        $dto = new CurriculumInventoryReportDTO(
            $report->getId(),
            $report->getName(),
            $report->getDescription(),
            $report->getYear(),
            $report->getStartDate(),
            $report->getEndDate(),
            $report->getToken(),
        );

        $dto->export = $report->getExport() ? (string) $report->getExport() : null;
        $dto->sequence = $report->getSequence() ? (string) $report->getSequence() : null;
        $dto->program = $report->getProgram() ? (string) $report->getProgram() : null;

        $sequenceBlockIds = $report->getSequenceBlocks()
            ->map(function (CurriculumInventorySequenceBlockInterface $block) {
                return (string) $block;
            });
        $dto->sequenceBlocks = $sequenceBlockIds->toArray();

        $academicLevelIds = $report->getAcademicLevels()
            ->map(function (CurriculumInventoryAcademicLevelInterface $level) {
                return (string) $level;
            });
        $dto->academicLevels = $academicLevelIds->toArray();

        return $dto;
    }
}
