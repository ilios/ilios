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
     * @var int
     * @IS\Id
     * @IS\Expose
     * @IS\Type("integer")
    */
    public $id;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $startYear;

    /**
     * @var bool
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $locked;

    /**
     * @var bool
     *
     * @IS\Expose
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $archived;

    /**
     * @var int
     * @IS\Expose
     * @IS\Related("programs")
     * @IS\Type("string")
     */
    public $program;

    /**
     * @var int
     * @IS\Expose
     * @IS\Related("cohorts")
     * @IS\Type("string")
    */
    public $cohort;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("users")
     * @IS\Type("array<string>")
     */
    public $directors;

    /**
     * @var CompetencyInterface[] $competencies
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public $competencies;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public $terms;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public $programYearObjectives;

    /**
     * For Voter use, not public
     * @var int
     */
    public $school;

    public function __construct(
        $id,
        $startYear,
        $locked,
        $archived
    ) {
        $this->id = $id;
        $this->startYear = $startYear;
        $this->locked = $locked;
        $this->archived = $archived;

        $this->directors = [];
        $this->competencies = [];
        $this->terms = [];
        $this->programYearObjectives = [];
    }
}
