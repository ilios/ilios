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
     * @IS\Id
     * @IS\Expose
     * @IS\Type("integer")
     */
    public int $id;

    /**
     * @IS\Expose
     * @IS\Type("string")
     *
    */
    public string $name;

    /**
     * @IS\Expose
     * @IS\Type("string")
    */
    public ?string $description;

    /**
     * @IS\Expose
     * @IS\Type("integer")
     */
    public int $level;

    /**
     * @IS\Expose
     * @IS\Related("curriculumInventoryReports")
     * @IS\Type("integer")
     */
    public int $report;

    /**
    * @var int[]
    * @IS\Expose
    * @IS\Related("curriculumInventorySequenceBlocks")
    * @IS\Type("array<string>")
    */
    public array $sequenceBlocks = [];

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
