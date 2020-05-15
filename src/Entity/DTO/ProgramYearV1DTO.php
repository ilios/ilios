<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;
use App\Entity\CompetencyInterface;

/**
 * Class ProgramYearV1DTO
 * Data transfer object for a programYear
 *
 * @IS\DTO
 */
class ProgramYearV1DTO
{

    /**
     * @var int
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
     * @var bool
     * @IS\Expose
     * @IS\Type("boolean")
     * @deprecated
     */
    public $publishedAsTbd;

    /**
     * @var bool
     * @IS\Expose
     * @IS\Type("boolean")
     * @deprecated
     */
    public $published;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $program;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $cohort;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $directors;

    /**
     * @var CompetencyInterface[] $competencies
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $competencies;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $terms;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $objectives;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $stewards;

    /**
     * For Voter use, not public
     * @var int
     */
    public $school;

    public function __construct(
        $id,
        $startYear,
        $locked,
        $archived,
        $publishedAsTbd,
        $published
    ) {
        $this->id = $id;
        $this->startYear = $startYear;
        $this->locked = $locked;
        $this->archived = $archived;
        $this->publishedAsTbd = $publishedAsTbd;
        $this->published = $published;

        $this->directors = [];
        $this->competencies = [];
        $this->terms = [];
        $this->objectives = [];
        $this->stewards = [];
    }
}
