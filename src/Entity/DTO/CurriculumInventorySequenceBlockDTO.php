<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;
use DateTime;

/**
 * Class CurriculumInventorySequenceBlockDTO
 *
 * @IS\DTO("curriculumInventorySequenceBlocks")
 */
class CurriculumInventorySequenceBlockDTO
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
    public string $title;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $description;

    /**
     * @IS\Expose
     * @IS\Type("integer")
     */
    public int $required;

    /**
     * @IS\Expose
     * @IS\Type("integer")
     */
    public int $childSequenceOrder;

    /**
     * @IS\Expose
     * @IS\Type("integer")
     */
    public int $orderInSequence;

    /**
     * @IS\Expose
     * @IS\Type("integer")
     */
    public int $minimum;

    /**
     * @IS\Expose
     * @IS\Type("integer")
     */
    public int $maximum;

    /**
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public bool $track;

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
     * @IS\Type("integer")
     */
    public int $duration;

    /**
     * @IS\Expose
     * @IS\Related("curriculumInventoryAcademicLevels")
     * @IS\Type("string")
     */
    public int $academicLevel;

    /**
     * @IS\Expose
     * @IS\Related("courses")
     * @IS\Type("string")
     */
    public ?int $course;

    /**
     * @IS\Expose
     * @IS\Related("curriculumInventorySequenceBlocks")
     * @IS\Type("string")
     */
    public ?int $parent;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("curriculumInventorySequenceBlocks")
     * @IS\Type("array<string>")
     */
    public array $children;

    /**
     * @IS\Expose
     * @IS\Related("curriculumInventoryReports")
     * @IS\Type("string")
     */
    public int $report;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $sessions;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("sessions")
     * @IS\Type("array<string>")
     */
    public array $excludedSessions;

    /**
     * Needed for voting not exposed in the API
     *
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
        DateTime $startDate,
        DateTime $endDate,
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

        $this->children = [];
        $this->sessions = [];
        $this->excludedSessions = [];
    }
}
