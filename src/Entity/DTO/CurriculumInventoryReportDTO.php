<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

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
}
