<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class CurriculumInventorySequenceBlockDTO
 *
 * @IS\DTO("curriculumInventorySequenceBlocks")
 */
class CurriculumInventorySequenceBlockDTO
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
     * @IS\Expose
     * @IS\Type("string")
     */
    public $title;

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
    public $required;

    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $childSequenceOrder;

    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $orderInSequence;

    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $minimum;

    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $maximum;

    /**
     * @var bool
     *
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $track;

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
     * @IS\Type("integer")
     */
    public $duration;

    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Related("curriculumInventoryAcademicLevels")
     * @IS\Type("string")
     */
    public $academicLevel;

    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Related("courses")
     * @IS\Type("string")
     */
    public $course;

    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Related("curriculumInventorySequenceBlocks")
     * @IS\Type("string")
     */
    public $parent;

    /**
     * @var array
     *
     * @IS\Expose
     * @IS\Related("curriculumInventorySequenceBlocks")
     * @IS\Type("array<string>")
     */
    public $children;

    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Related("curriculumInventoryReports")
     * @IS\Type("string")
     */
    public $report;

    /**
     * @var array
     *
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public $sessions;

    /**
     * @var array
     *
     * @IS\Expose
     * @IS\Related("sessions")
     * @IS\Type("array<string>")
     */
    public $excludedSessions;

    /**
     * Needed for voting not exposed in the API
     *
     * @var int
     *
     */
    public $school;

    /**
     * CurriculumInventorySequenceBlockDTO constructor.
     * @param $id
     * @param $title
     * @param $description
     * @param $required
     * @param $childSequenceOrder
     * @param $orderInSequence
     * @param $minimum
     * @param $maximum
     * @param $track
     * @param $startDate
     * @param $endDate
     * @param $duration
     */
    public function __construct(
        $id,
        $title,
        $description,
        $required,
        $childSequenceOrder,
        $orderInSequence,
        $minimum,
        $maximum,
        $track,
        $startDate,
        $endDate,
        $duration
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
