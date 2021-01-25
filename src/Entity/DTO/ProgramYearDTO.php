<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;
use App\Entity\CompetencyInterface;

/**
 * Class ProgramYearDTO
 * Data transfer object for a programYear
 *
 * @IS\DTO("programYears")
 */
class ProgramYearDTO
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
    public int $startYear;

    /**
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public bool $locked;

    /**
     * @IS\Expose
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public bool $archived;

    /**
     * @IS\Expose
     * @IS\Related("programs")
     * @IS\Type("integer")
     */
    public int $program;

    /**
     * @IS\Expose
     * @IS\Related("cohorts")
     * @IS\Type("integer")
    */
    public int $cohort;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("users")
     * @IS\Type("array<string>")
     */
    public array $directors = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $competencies = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $terms = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $programYearObjectives = [];

    /**
     * For Voter use, not public
     */
    public int $school;

    public function __construct(
        int $id,
        int $startYear,
        bool $locked,
        bool $archived
    ) {
        $this->id = $id;
        $this->startYear = $startYear;
        $this->locked = $locked;
        $this->archived = $archived;
    }
}
