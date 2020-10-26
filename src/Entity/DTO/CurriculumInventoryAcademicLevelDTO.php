<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class CurriculumInventoryAcademicLevelDTO
 *
 * @IS\DTO("curriculumInventoryAcademicLevels")
 */
class CurriculumInventoryAcademicLevelDTO
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
     *
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
    public $level;

    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Related("curriculumInventoryReports")
     * @IS\Type("integer")
     */
    public $report;

    /**
    * @var array
    *
    * @IS\Expose
    * @IS\Related("curriculumInventorySequenceBlocks")
    * @IS\Type("array<string>")
    */
    public $sequenceBlocks;

    /**
     * Needed for voting not exposed in the API
     *
     * @var int
     */
    public $school;

    /**
     * Constructor
     */
    public function __construct($id, $name, $description, $level)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->level = $level;

        $this->sequenceBlocks = [];
    }
}
